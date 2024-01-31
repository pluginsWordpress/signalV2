
<?php
/*
Plugin Name: Signal
Plugin URI: https://github.com/pluginsWordpress/signalV2/blob/main/signal.php
Description: Plugin de signal personnalisé pour WordPress
Version: 1.0
Author: Marouane
Author URI: https://github.com/marouane216
*/
// Fonction d'activation du plugin
function mon_plugin_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'signal';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nom varchar(255) NOT NULL,
        prenom varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        type_signal varchar(255) NOT NULL,
        raison_signal varchar(255) NOT NULL,
        commentaire varchar(255) NOT NULL,
        date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_name2 = $wpdb->prefix . 'formChoice';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name2 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        form text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'mon_plugin_activation');

// Fonction de désactivation du plugin
function mon_plugin_desactivation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'signal';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_deactivation_hook(__FILE__, 'mon_plugin_desactivation');
function signal_add_menu_page()
{
    add_menu_page(
        __('Signal', 'textdomain'),
        'Signal',
        'manage_options',
        'Signal',
        '',
        'dashicons-admin-plugins',
        6
    );
    add_submenu_page(
        'Signal',
        __('Books Shortcode Reference', 'textdomain'),
        __('Shortcode Reference', 'textdomain'),
        'manage_options',
        'Signal',
        'Signal_callback'
    );
}
add_action('admin_menu', 'signal_add_menu_page');

function Signal_callback()
{
    ?>
    <style>
        .form{
            margin-top: 10rem;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 50%;
            margin: 0 25%;
            justify-content: center;
        }

        form div {
            display: flex;
            flex-direction: row;
            justify-content: start;
        }

        form div label,
        form div input {
            cursor: pointer;
        }

        .Submit {
            background-color: #0d6efd;
            color: black;
            font-size: 1rem;
            width: 6rem;
            display: flex;
            justify-content: center;
            border: 1px solid;
            border-radius: 7px;
            cursor: pointer;
        }

        .Submit:hover {
            color: aliceblue;
        }
    </style>
     <!-- id="form" -->
    <form class="form" method="Post" action="<?=esc_url(admin_url('admin-post.php'))?>">
        <div>
            <input value="<div><label for=nom>nom:</label><input type=text name=nom></div>" type="radio" name="nom" id="nom">
            <label class="labelForm" for="nom">nom:</label>
        </div>
        <div>
            <input value="<div><label for=prenom>Prenom:</label><input type=text name=prenom></div>" type="radio" name="prenom" id="prenom">
            <label class="labelForm" for="prenom">prenom:</label>
        </div>
        <div>
            <input value='<div><label for=email>Email:</label><input type=email name=email></div>' type="radio" name="email" id="email">
            <label class="labelForm" for="email">Email:</label>
        </div>
        <div>
            <input value='<div><label for=type_signal>type de signal:</label><select name=type_signal><option value=type 1>type 1</option><option value=type 2>type 2</option><option value=type 3>type 3</option></select></div>' type="radio" name="type_signal" id="type_signal">
            <label class="labelForm" for="type_signal">le type de signal:</label>
        </div>
        <div>
            <input value='<div><label for=raison_signal>raison de signal:</label><select name=raison_signal><option value=raison 1>raison 1</option><option value=raison 2>raison 2</option><option value=raison 3>raison 3</option></select></div>' type="radio" name="raison_signal" id="raison_signal">
            <label class="labelForm" for="raison_signal">le raison de votre signal:</label>
        </div>
        <div>
            <input value='<div><label for=commentaire>commentaire:</label><textarea style=resize:none name=commentaire cols=30 rows=10></textarea></div>' type="radio" name="commentaire" id="commentaire">
            <label class="labelForm" for="commentaire">un commentaire:</label>
        </div>
        <div>
            <input type="hidden" name="action" value="mon_plugin_register_form">
            <input class="Submit" type="submit" value="Save">
        </div>
    </form>
    <?php
}
function mon_plugin_shortcode_signal()
{
    ob_start();
    ?>
    <style>
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 50%;
            margin: 0 25%;
        }

        form div {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .Submit {
            background-color: #0d6efd;
            color: black;
            font-size: 1rem;
            width: 6rem;
            display: flex;
            justify-content: center;
            border: 1px solid;
            border-radius: 7px;
            cursor: pointer;
        }
        .Submit:hover{
            color: aliceblue;
        }
    </style>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'formChoice';
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 1");

    echo $results[0]->form;
    return ob_get_clean();
}
add_shortcode('mon_plugin_form_signal', 'mon_plugin_shortcode_signal');
function mon_plugin_register()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'signal';


    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $type_signal = $_POST['type_signal'];
    $raison_signal = $_POST['raison_signal'];
    $commentaire = $_POST['commentaire'];

    $wpdb->insert(
        $table_name,
        array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'type_signal' => $type_signal,
            'raison_signal' => $raison_signal,
            'commentaire' => $commentaire
        )
    );

    wp_redirect(home_url(''));
    exit;
}
add_action('admin_post_mon_plugin_register', 'mon_plugin_register');
function mon_plugin_register_form()
{
    error_reporting(0);
    global $wpdb;
    $table_name = $wpdb->prefix . 'formChoice';
    
    if ($_POST['nom'] == null) {
        $nom = '<input type="hidden" name="nom" value="">';
    } else {
        $nom = $_POST['nom'];
    }    
    if ($_POST['prenom'] == null) {
        $prenom = '<input type="hidden" name="prenom" value="">';
    } else {
        $prenom = $_POST['prenom'];
    }    
    if ($_POST['email'] == null) {
        $email = '<input type="hidden" name="email" value="">';
    } else {
        $email = $_POST['email'];
    }    
    if ($_POST['type_signal'] == null) {
        $type_signal = '<input type="hidden" name="type_signal" value="">';
    } else {
        $type_signal = $_POST['type_signal'];
    }    
    if ($_POST['raison_signal'] == null) {
        $raison_signal = '<input type="hidden" name="raison_signal" value="">';
    } else {
        $raison_signal = $_POST['raison_signal'];
    }    
    if ($_POST['commentaire'] == null) {
        $commentaire = '<input type="hidden" name="commentaire" value="">';
    } else {
        $commentaire = $_POST['commentaire'];
    }

    $form = '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
        $form.=$nom;
        $form.=$prenom;
        $form.=$email;
        $form.=$type_signal;
        $form.=$raison_signal;
        $form.=$commentaire;
        $form.='<div>';
            $form.='<input type="hidden" name="action" value="mon_plugin_register">';
            $form.='<input class="Submit" type="submit" value="Envoyer">';
        $form.='</div>';
    $form .= '</form>';
    $wpdb->insert(
        $table_name,
        array(
            'form' => $form
        )
    );

    wp_redirect(home_url('/wp-admin/admin.php?page=affiche_Signal'));
    exit;
}
add_action('admin_post_mon_plugin_register_form', 'mon_plugin_register_form');


function affiche_signal_add_menu_page()
{
    add_menu_page(
        __('affiche_Signal', 'textdomain'),
        'affiche_Signal',
        'manage_options',
        'affiche_Signal',
        '',
        'dashicons-admin-home',
        6
    );
    add_submenu_page(
        'affiche_Signal',
        __('Books Shortcode Reference', 'textdomain'),
        __('Shortcode Reference', 'textdomain'),
        'manage_options',
        'affiche_Signal',
        'affiche_Signal_callback'
    );
}
add_action('admin_menu', 'affiche_signal_add_menu_page');

function affiche_Signal_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'signal';

    $results = $wpdb->get_results("SELECT * FROM $table_name");
?>
    <style>
        #myTable {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 1rem;
        }

        #myTable th,
        #myTable td {
            text-align: left;
            padding: 8px;
        }

        #myTable th {
            background-color: #f2f2f2;
        }

        #myTable tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #myTable tr:hover {
            background-color: #ddd;
        }

        #myTable tr {
            border-bottom: 1px solid black;
        }

        #myTable td,
        #myTable th {
            border-right: 1px solid black;
            padding: 5px;
            /* facultatif : pour ajouter de l'espace autour du contenu des cellules */
        }

        .actionDiv {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            width: auto;
        }

        .action {
            width: auto;
            height: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-sizing: border-box;
            border: 1.20968px solid #000000;
            border-radius: 4.83871px;
            color: white;
        }

        .delete {
            background-color: #FF0000;
        }

        .edit {
            background-color: #80FF00;
        }

        .edit a i {
            color: white;
        }

        .Role {
            background: #00d1ff;
        }

        .btnExport {
            background-color: green;
            border: 1px solid black;
            color: #ffffff;
            cursor: pointer;
        }

        .btnExport:hover {
            background-color: lime;
            color: black;
        }

        a {
            color: #ffb40a;
            text-decoration: none;
        }
    </style>
    <table class="table" id="myTable">
        <thead>
            <tr>
                <th scope="col">Nom</th>
                <th scope="col">Prenom</th>
                <th scope="col">Email</th>
                <th scope="col">Le type de signal</th>
                <th scope="col">Le raison de votre Signal</th>
                <th scope="col">Commentaire</th>
                <th scope="col">Date</th>
                <th scope="col">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result) { ?>
                <tr>
                    <td><?= $result->nom ?></td>
                    <td><?= $result->prenom ?></td>
                    <td>
                        <a href="mailto:<?= $result->email ?>">
                            <?= $result->email ?>
                        </a>
                    </td>
                    <td><?= $result->type_signal ?></td>
                    <td><?= $result->raison_signal ?></td>
                    <td><?= $result->commentaire ?></td>
                    <td><?= $result->date ?></td>
                    <td class="actionDiv">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="delete_Contact">
                            <input type="hidden" name="id_contact" value="<?= $result->id ?>">
                            <button title="delete" type="submit" class="action delete">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21.68 5H17.72V3.8C17.72 3.05739 17.3955 2.3452 16.8179 1.8201C16.2403 1.295 15.4569 1 14.64 1H9.36C8.54313 1 7.75972 1.295 7.18211 1.8201C6.6045 2.3452 6.28 3.05739 6.28 3.8V5H2.32C1.96991 5 1.63417 5.12643 1.38662 5.35147C1.13907 5.57652 1 5.88174 1 6.2C1 6.51826 1.13907 6.82348 1.38662 7.04853C1.63417 7.27357 1.96991 7.4 2.32 7.4H2.76V21C2.76 21.5304 2.99179 22.0391 3.40437 22.4142C3.81695 22.7893 4.37652 23 4.96 23H19.04C19.6235 23 20.1831 22.7893 20.5956 22.4142C21.0082 22.0391 21.24 21.5304 21.24 21V7.4H21.68C22.0301 7.4 22.3658 7.27357 22.6134 7.04853C22.8609 6.82348 23 6.51826 23 6.2C23 5.88174 22.8609 5.57652 22.6134 5.35147C22.3658 5.12643 22.0301 5 21.68 5ZM8.92 3.8C8.92 3.69391 8.96636 3.59217 9.04887 3.51716C9.13139 3.44214 9.2433 3.4 9.36 3.4H14.64C14.7567 3.4 14.8686 3.44214 14.9511 3.51716C15.0336 3.59217 15.08 3.69391 15.08 3.8V5H8.92V3.8ZM18.6 20.6H5.4V7.4H18.6V20.6ZM10.68 10.6V17C10.68 17.3183 10.5409 17.6235 10.2934 17.8485C10.0458 18.0736 9.71009 18.2 9.36 18.2C9.00991 18.2 8.67417 18.0736 8.42662 17.8485C8.17907 17.6235 8.04 17.3183 8.04 17V10.6C8.04 10.2817 8.17907 9.97652 8.42662 9.75147C8.67417 9.52643 9.00991 9.4 9.36 9.4C9.71009 9.4 10.0458 9.52643 10.2934 9.75147C10.5409 9.97652 10.68 10.2817 10.68 10.6ZM15.96 10.6V17C15.96 17.3183 15.8209 17.6235 15.5734 17.8485C15.3258 18.0736 14.9901 18.2 14.64 18.2C14.2899 18.2 13.9542 18.0736 13.7066 17.8485C13.4591 17.6235 13.32 17.3183 13.32 17V10.6C13.32 10.2817 13.4591 9.97652 13.7066 9.75147C13.9542 9.52643 14.2899 9.4 14.64 9.4C14.9901 9.4 15.3258 9.52643 15.5734 9.75147C15.8209 9.97652 15.96 10.2817 15.96 10.6Z" fill="white" />
                                </svg>

                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <button class="btnExport" onclick="exportTableToExcel('myTable')">Export to Excel</button>
    <script>
        function exportTableToExcel(tableID, filename = '') {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            // Specify file name
            filename = filename ? filename + '.xls' : 'excel_data.xls';

            // Create download link element
            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            if (navigator.msSaveOrOpenBlob) {
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

                // Setting the file name
                downloadLink.download = filename;

                //triggering the function
                downloadLink.click();
            }
        }
    </script>
<?php
}
function delete_Contact()
{
    ob_start();
    global $wpdb;
    $table_name = $wpdb->prefix . 'signal';

    $id = $_POST['id_contact'];

    $wpdb->get_results("DELETE FROM $table_name WHERE id = $id");

    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = wp_get_referer();
        wp_redirect($referer);
        return ob_get_clean();
    }
}
add_action('admin_post_delete_Contact', 'delete_Contact');
?>

