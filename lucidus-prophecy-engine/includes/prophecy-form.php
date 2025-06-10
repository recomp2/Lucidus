<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_render_prophecy_form() {
    ob_start();
    ?>
    <form id="lpe-prophecy-form">
        <p><label>Username / Latin Name<br>
            <input type="text" name="lpe_username" required></label></p>
        <p><label>Date of Birth<br>
            <input type="date" name="lpe_dob" required></label></p>
        <p><label>Town / Country<br>
            <input type="text" name="lpe_town" required></label></p>
        <p>Archetype:<br>
            <label><input type="radio" name="lpe_archetype" value="Dub" required> Dub</label>
            <label><input type="radio" name="lpe_archetype" value="Randall"> Randall</label>
            <label><input type="radio" name="lpe_archetype" value="Nasty P"> Nasty P</label>
        </p>
        <p><label>Favorite Strain / Plant (optional)<br>
            <input type="text" name="lpe_strain"></label></p>
        <p><label>Mood / Question (optional)<br>
            <textarea name="lpe_question"></textarea></label></p>
        <p><button type="submit">Reveal Prophecy</button></p>
    </form>
    <div id="lpe-prophecy-output"></div>
    <?php
    return ob_get_clean();
}
