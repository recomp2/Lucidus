<?php
/* Template Name: Lucidus Archive */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$archives = lucidus_get_archive_entries();
?>
<div id="lucidus-archive">
    <h2><?php esc_html_e('Lucidus Archive', 'dead-bastard-society-theme'); ?></h2>
    <ul>
        <?php foreach ($archives as $item): ?>
            <li><?php echo esc_html($item['time'] . ' - ' . $item['message']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
get_footer();
?>
