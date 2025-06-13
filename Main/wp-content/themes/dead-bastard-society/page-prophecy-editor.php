<?php
/* Template Name: Prophecy Editor */
get_header();
if(function_exists('lucidus_memory_trigger')){ lucidus_memory_trigger(); }
?>
<main>
<h1>Prophecy Editor</h1>
<form id="prophecy-form">
    <textarea name="text" rows="4" style="width:100%"></textarea>
    <p><button type="submit">Save Prophecy</button></p>
</form>
<div id="prophecy-output"></div>
</main>
<script>
document.getElementById('prophecy-form').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const text = e.target.text.value;
    await fetch('<?php echo esc_url( rest_url("lucidus-core/v1/prophecy") ); ?>', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({text})
    });
    loadProphecy();
    e.target.reset();
});
async function loadProphecy(){
    const res = await fetch('<?php echo esc_url( rest_url("lucidus-core/v1/prophecy") ); ?>');
    const data = await res.json();
    document.getElementById('prophecy-output').textContent = data ? data.prophecy : 'None';
}
loadProphecy();
</script>
<?php get_footer(); ?>
