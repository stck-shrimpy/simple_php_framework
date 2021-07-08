<?php

namespace lib\Controller;

use LogicException;
use Throwable;
use lib\Logger;

abstract class BaseController
{
    protected $method = 'GET';
    protected $params = [];
    protected $files  = [];
    protected $envs   = [];
    protected $logger = null;

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function getQueryParams()
    {
        parse_str($this->getEnv('query_string'), $query_params);
        return $query_params;
    }

    public function getParam($key)
    {
        $param = null;
        if (isset($this->params[$key])) {
            if (is_array($this->params[$key])) {
                $param = [];
                foreach ($this->params[$key] as $_key => $value) {
                    $param[$_key] = ($value === '') ? null : trim($value);
                }

                return $param;
            }

            $param = ($this->params[$key] === '') ? null : trim($this->params[$key]);
        }

        return $param;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    public function getFile($key): array
    {
        $file = [];
        if (isset($this->files[$key])) {
            $file = $this->files[$key];
            if (!empty($file['tmp_name']) && $file['size'] > 0) {
                $file['data'] = file_get_contents($file['tmp_name']);
            } else {
                $file = [];
            }
        }

        return $file;
    }

    public function setEnvs(array $envs)
    {
        foreach ($envs as $key => $value) {
            $this->setEnv($key, $value);
        }

        if ($_method = $this->getEnv('request-method')) {
            $this->setMethod($_method);
        }
    }

    public function setEnv($key, $value)
    {
        $this->envs[$this->normalizeEnvKey($key)] = $value;
    }

    public function getEnvs()
    {
        return $this->envs;
    }

    public function getEnv(string $key)
    {
        $_key = $this->normalizeEnvKey($key);
        if (isset($this->envs[$_key])) {
            return $this->envs[$_key];
        }

        return null;
    }

    public function setMethod(string $method)
    {
        $_method = strtoupper($method);
        $allowed_methods = ['GET', 'POST', 'PUT', 'DELETE'];
        if (in_array($_method, $allowed_methods)) {
            $this->method = $_method;
        } else {
            throw new LogicException(__METHOD__ . '() メソッドが存在しません: ' . $method);
        }
    }

    public function execute(string $action)
    {
        try {
            $this->setup();

            if (!method_exists($this, $action)) {
                throw new LogicException(__METHOD__ . "() アクションが見つかりません");
            }

            $this->$action();
        } catch (Throwable $e) {
            $this->err500($e->getMessage());
        }
    }

    public function getQueryUri(string $uri, array $params = [])
    {
        if (!empty($params)) {
            $glue = (strpos($uri, '?') === false) ? '?' : '&';
            $uri .= $glue . http_build_query($params, '', '&');
        }

        return $uri;
    }

    public function redirect(string $uri, array $params = [], bool $exit = true)
    {
        header('Location: ' . get_uri($uri, $params));

        if ($exit) {
            exit;
        }
    }

    public function setUp()
    {
        $this->logger = new Logger();
    }

    public function log(string $message, $errType = E_ALL)
    {
        if (isset($this->logger)) {
            $message = $this->getEnv('remote_addr') . ' '
                     . $this->getEnv('request-uri') . ' '
                     . $message;

            $this->logger->write($message, $errType);
        }
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $message = $errstr;

        if (!empty($errfile)) {
            $message .= ' file: ' . $errfile;
        }

        if (!empty($errline)) {
            $message .= ' line: ' . $errline;
        }

        $this->log($message, $errno);
    }

    public function err400(string $message = '', bool $exit = true)
    {
        $protocol = $this->getEnv('server_protocol');
        header("{$protocol} 400 Bad Request");

        $this->render('errors/400.php', [
          'message'     => $message,
          'request_uri' => $this->getEnv('request_uri'),
        ]);

        $this->log('400 Bad Request ' . $message, E_WARNING);

        if ($exit) {
          exit;
        }
    }

    public function err404(string $message = '', bool $exit = true)
    {
        $protocol = $this->getEnv('server_protocol');
        header("{$protocol} 404 Not Found");

        $this->render('errors/404.php', [
          'message'     => $message,
          'request_uri' => $this->getEnv('request_uri'),
        ]);

        $this->log('404 Not Found ' . $message, E_NOTICE);

        if ($exit) {
          exit;
        }
    }

    public function err500(string $message = '', bool $exit = true)
    {
        $protocol = '';
        header("{$protocol} 500 Internal Server Error");

        $this->render('errors/500.php', [
            'message'     => $message,
            'request_uri' => $this->getEnv('request_uri'),
        ]);

        $this->log('500 Internal Server Error ' . $message, E_ERROR);
        if ($exit) {
            exit;
        }
    }

    protected function getTemplate(string $name)
    {
        $path = HTML_DIR . '/' . $name;

        return file_exists($path) ? $path : null;
    }

    protected function render(string $template_name, array $data = [])
    {
        $template = $this->getTemplate($template_name);
        if (!empty($template)) {
            extract(array_merge(get_object_vars($this), $data), EXTR_OVERWRITE);
            include($template);
        }
    }

    protected function normalizeEnvKey(string $key)
    {
        return strtolower(str_replace('-', '_', $key));
    }
}
