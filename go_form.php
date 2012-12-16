<?php

function get_main_form ( $domain_lang, $lang_enabled=array(), $lang_set, $msg ) { ?>
    
    <div style="float:left;" id="main_wrapper">
        <h1>Заполнить пустоту переводами</h1>
         <form id="lanuages_options_data" method="POST">
            <table>
                <thead>
                    <td>Домен</td>
                    <td>Язык</td>
                    <td>Переводить?</td>
                </thead>
                <tbody>                
                    <?php

                    foreach ($domain_lang as $domain => $lang): ?>
                        
                        <tr>
                            <td>
                                <input type="text" name="domain" value="<?= $domain ?>">
                            </td>

                            <td>
                                <select name="lang">
                                    <?php foreach ($lang_set as $lang_code => $lang_title): ?>
                                        <option value="<?= $lang_code ?>" <?php if( $lang_code==$lang ): ?> selected="selected" <?php endif; ?>><?= $lang_title ?></option>
                                    <?php endforeach; ?>
                                </select>  
                            </td>

                            <td style="text-align:center;">
                                <input type="checkbox" name="enabled" <?php if ( in_array($lang, $lang_enabled) ): ?>checked="checked"<?php endif; ?>>
                            </td>

                            <td><div class="remove_language"></div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div type="button" class="add_language">Добавить перевод</div><div id="save_options" class="buttn">Сохранить настройки</div>
        </form><br>

        <div id="msg_field"><?= $msg; ?></div>

        <form method="POST" id="tr_options">
            <input type="hidden" name="lang_options">
        </form>
        
        <form method="POST" id="translate_posts">
            <select name="post_status">
                <option value="draft">Черновик</option>
                <option value="publish">Опубликованные записи</option>
                <option value="future">Будущие</option>
                <option value="private">Приватные</option>
            </select>
            <input type="submit" id="translate" value="Перевести все">
            <input type="hidden" name="go_trans_options">
        </form><br>

        <form method="POST">
            <select name="delete_translated_posts">
                <option value="all">все</option>
                <?php foreach ($domain_lang as $domain => $lang): if($lang=='ru') continue; ?>
                    <option value="<?= $lang ?>"><?= $lang_set[$lang] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Удалить переведенные посты">
        </form>
    </div>
<?php
}