<?php View::get('side'); ?>
<div id="app-content">
    <?php MonthSwitcher::display($_SESSION['month'], $_SESSION['year']) ?>
    
    <?php 
        $totalReal = 0;
        $totalToRempla = 0;
        $totalPaid = 0;
    ?>
    <table id="recap-rempla">
        <?php foreach(View::data('totalsPaid') as $name => $recap): ?>
        <?php 
            $totalReal += $recap['total_real'];
            $totalToRempla += $recap['total_to_rempla'];
            $totalPaid += $recap['total_paid'];
        ?>
        <tr>
            <td class="label"><?php echo $name; ?></td>
            <td><?php echo View::formatAmount($recap['total_real']); ?></td>
            <td class="label"><?php echo $name; ?></td>
            <td><?php echo View::formatAmount($recap['total_to_rempla']); ?></td>
            <td class="label"><?php echo $name; ?></td>
            <td><?php echo View::formatAmount($recap['total_paid']); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td class="label"><b>Total perçu</b></td>
            <td><?php echo View::formatAmount($totalReal); ?></td>
            <td class="label"><b>Total dû</b></td>
            <td><?php echo View::formatAmount($totalToRempla); ?></td>
            <td class="label"><b>Total paiement</b></td>
            <td><?php echo View::formatAmount($totalPaid); ?></td>
        </tr>
    </table>
    
    <?php if(!App::isLoggedUserAdmin()): ?>
    <div id="add-row-content">
        <form action="/rempla" method="post">
            <select name="who">
                <?php foreach(View::data('remplas') as $rempla): ?>
                <option value="<?php echo $rempla->id; ?>" <?php echo ($rempla->id === View::data('current_rempla')) ? 'selected="selected"' : ''; ?>><?php echo $rempla->name; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="date" name="date" maxlength="10" value="<?php echo (View::data('date')) ? View::data('date') : date('d/m/Y'); ?>" />
            <input type="text" id="patient-name" maxlength="30" name="patient_name" value="<?php echo (View::data('patient_name')) ? View::data('patient_name') : 'Nom du patient'; ?>" />
            <input type="text" id="amount" name="amount" value="<?php echo (View::data('amount')) ? View::data('amount') : 'Montant'; ?>" />
            <input type="submit" id="add-but" value="Ajouter" />
        </form>
    </div>
    <?php endif; ?>
    
    <?php foreach(View::data('prestations') as $name => $prestations): ?>
    <table class="prestations">
        <thead>
            <tr>
                <th colspan="4"><?php echo $name; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>
            <?php foreach($prestations as $prestation): ?>
            <?php $total += $prestation->amount; ?>
            <tr>
                <td>
                    <?php if(!App::isLoggedUserAdmin()): ?>
                    <a class="delete" href="/<?php echo App::getCurrentRoute(); ?>/prestations/<?php echo $prestation->id; ?>/delete">x</a>
                    <?php endif; ?>
                </td>
                <td><?php echo View::formatDate($prestation->date); ?></td>
                <td><?php echo $prestation->patient_name; ?></td>
                <td class="amount"><?php echo View::formatAmount($prestation->amount); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="3">Total</td>
                <td><?php echo View::formatAmount($total); ?></td>
            </tr>
        </tbody>
    </table>
    <?php endforeach; ?>
</div>