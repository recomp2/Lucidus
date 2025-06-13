<?php
/* Template Name: Scroll Wall */
get_header();
if(function_exists('lucidus_memory_trigger')){ lucidus_memory_trigger(); }
?>
<main class="lucidus-container">
  <h1>Scroll Wall</h1>
  <ul class="scroll-feed">
  <?php
    $dir = WP_CONTENT_DIR.'/dbs-library/memory-archive';
    foreach(glob($dir.'/chatlog_*.json') as $file){
        $data = json_decode(file_get_contents($file), true);
        if($data){
            foreach($data as $entry){
                echo '<li>'.esc_html($entry['time'].': '.$entry['message']).'</li>';
            }
        }
    }
  ?>
  </ul>
</main>
<?php get_footer(); ?>
