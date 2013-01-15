<a id="logout" href="/logout">x</a>
<div id="side">
    <a <?php echo (View::data('header_selected') === 'main') ? 'class="menu-selected"' : ''; ?> href="/dashboard">Général</a>
    <a <?php echo (View::data('header_selected') === 'collab') ? 'class="menu-selected"' : ''; ?> href="/collab">Collab & Assoc</a>
    <a <?php echo (View::data('header_selected') === 'rempla') ? 'class="menu-selected"' : ''; ?> href="/rempla">Remplacement</a>
</div>