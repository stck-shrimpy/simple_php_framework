document.addEventListener('DOMContentLoaded', function() {
    const check_all   = document.getElementById('js-check-all');
    const check_boxes = document.querySelectorAll('td input[type="checkbox"]');

    check_all.addEventListener('change', function() {
        for (let i = 0; i < check_boxes.length; i++) {
            check_boxes[i].checked = check_all.checked;
        }
    });

    const delete_many_btn = document.getElementById('js-delete-many-btn');
    delete_many_btn.addEventListener('click', function() {
        let is_checkbox_checked = false;
        for (let i = 0; i < check_boxes.length; i++) {
            if (check_boxes[i].checked) {
                is_checkbox_checked = true;
                break;
            }
        }

        if (!is_checkbox_checked) {
            alert('削除する投稿にチェックを入れてください');
            e.preventDefault();
        } else {
            if (!confirm('本当に削除しますか？')) {
                e.preventDefault();
            }
        }
    })

    const hidden_form  = document.getElementById('js-hidden-form');
    const hidden_input = document.getElementById('js-hidden-input');

    const submitHiddenForm = function(action, name, value) {
        hidden_form.action = action;
        hidden_input.name  = name;
        hidden_input.value = value;

        hidden_form.submit();
    }

    const delete_one_btns = document.querySelectorAll('.js-delete-one-btn');
    for (let i = 0; i < delete_one_btns.length; i++) {
        delete_one_btns[i].addEventListener('click', function(e) {
            if (confirm('本当に削除しますか？')) {
                submitHiddenForm(e.target.getAttribute('form_action'), 'post_ids[]', e.target.getAttribute('post_id'));
            }

            e.preventDefault();
        });
    }

    const delete_image_btns = document.querySelectorAll('.js-delete-image-btn');
    for (let i = 0; i < delete_image_btns.length; i++) {
        delete_image_btns[i].addEventListener('click', function(e) {
            if (confirm('本当に削除しますか？')) {
                submitHiddenForm(e.target.getAttribute('form_action'), 'post_id', e.target.getAttribute('post_id'));
            }

            e.preventDefault();
        });
    }

    const recover_one_btns = document.querySelectorAll('.js-recover-one-btn');
    for (let i = 0; i < recover_one_btns.length; i++) {
        recover_one_btns[i].addEventListener('click', function(e) {
            if (confirm('本当に復元しますか？')) {
                submitHiddenForm(e.target.getAttribute('form_action'), 'post_id', e.target.getAttribute('post_id'));
            }

            e.preventDefault();
        });
    }
});

