<?php

function get_main_form ( $go_trans_options, $lang_set, $msg ) {
?>
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
                    if ( empty($go_trans_options) )
                        $go_trans_options = array( 'ru-Ru' => array( 'enabled'=>0, 'lang_domain'=>'ru' ) );

                    foreach ($go_trans_options as $domain => $options): ?>
                        
                        <tr>
                            <td>
                                <input type="text" name="domain" value="<?= $domain ?>">
                            </td>

                            <td>
                                <select name="lang_domain">
                                    <?php foreach ($lang_set as $lang_code => $lang_title): ?>
                                        <option value="<?= $lang_code ?>" <?php if( $lang_code==$options['lang_domain'] ): ?> selected="selected" <?php endif; ?>><?= $lang_title ?></option>
                                    <?php endforeach; ?>
                                </select>  
                            </td>

                            <td style="text-align:center;">
                                <input type="checkbox" name="enabled" <?php if ( $options['enabled']==1 ): ?>checked="checked"<?php endif; ?>>
                            </td>

                            <td><div class="remove_language"></div></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div type="button" class="add_language">Добавить перевод</div><div id="save_options" class="buttn">Сохранить настройки</div>
        </form><br>

        <div id="msg_field"><?= $msg; ?></div>

        <form method="POST" id="lang_options">
            <input type="hidden" name="lang_options">
        </form>
        
        <form method="POST" id="options_serialized">
            <select name="post_status">
                <option value="draft">Черновик</option>
                <option value="publish">Опубликованные записи</option>
                <option value="future">Будущие</option>
                <option value="private">Приватные</option>
            </select>
            <input type="submit" id="translate" value="Translate">
            <input type="hidden" name="go_trans_options">
        </form><br>

        <form method="POST">
            <input type="hidden" name="delete_translations_cache">
            <input type="submit" value="Удалить кеш переводов">
        </form><br>

        <form method="POST">
            <select name="delete_translated_posts">
                <option value="en_post_id">английский</option>
                <option value="fr_post_id">французский</option>
                <option value="cn_post_id">китайский</option>
            </select>
            <input type="submit" value="Удалить переведенные посты">
        </form><br>

        <form method="POST">
            <input type="hidden" name="delete_all_translated_posts">
            <input type="submit" value="Удалить все переведенные посты">
        </form>
    </div>
<?php
}