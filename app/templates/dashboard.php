<?php View::get('side'); ?>
<div id="app-content">
    <?php MonthSwitcher::display($_SESSION['month'], $_SESSION['year']) ?>
    
    <?php 
        $salary = 0;
    ?>
    <div id="salary-tax">
        <table class="recap-rempla">
            <?php foreach(View::data('recap') as $name => $recap): ?>
            <?php $salary += $recap; ?>
            <tr>
                <td class="label"><?php echo $name; ?></td>
                <td><?php echo View::formatAmount($recap); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td class="label"><b>Salaire du mois</b></td>
                <td><?php echo View::formatAmount($salary); ?></td>
            </tr>
        </table>
        <table class="recap-rempla">
            <tr>
                <td class="label">Salaires</td>
                <td><?php echo View::formatAmount(View::data('fullSalary')); ?></td>
            </tr>
            <tr>
                <td class="label">Extras</td>
                <td><?php echo View::formatAmount(View::data('extrasAmount')); ?></td>
            </tr>
            <tr>
                <td class="label">Conservé pour les charges (<?php echo View::data('keepForChargesPercent'); ?>%)</td>
                <td><?php echo View::formatAmount(View::data('keepForCharges')); ?></td>
            </tr>
            <tr>
                <td class="label">Charges payés</td>
                <td><?php echo View::formatAmount(View::data('chargesAmount')); ?></td>
            </tr>
            <tr>
                <td class="label"><b>A conserver pour les charges à venir</b></td>
                <td><?php echo View::formatAmount(View::data('keepForCharges') - View::data('chargesAmount')); ?></td>
            </tr>
        </table>
    </div>
    
    <form method="post">
        <?php if(!App::isLoggedUserAdmin()): ?>
        <div id="add-row-content" class="collab-row">
            <label>Date</label>
            <input type="text" id="date" name="date" maxlength="10" value="<?php echo (View::data('date')) ? View::data('date') : date('d/m/Y'); ?>" />
            <input type="text" id="name" maxlength="30" name="name" value="<?php echo (View::data('name')) ? View::data('name') : 'Nom'; ?>" />
            <input type="text" id="amount" name="amount" value="<?php echo (View::data('amount')) ? View::data('amount') : 'Montant'; ?>" />
            <input type="submit" id="add-charge-but" name="add_charge" value="Charge" />
            <input type="submit" id="add-extra-but" name="add_extra" value="Extra" />
        </div>
        <?php endif; ?>
    
        <table class="prestations">
            <thead>
                <tr>
                    <th colspan="4">Charges payés</th>
                </tr>
            </thead>
            <tbody>
                <?php if(is_array(View::data('charges'))): ?>
                    <?php $total = 0; ?>
                    <?php foreach(View::data('charges') as $charge): ?>
                    <?php $total += $charge->amount; ?>
                    <tr>
                        <td>
                            <?php if(!App::isLoggedUserAdmin()): ?>
                            <a class="delete" href="/<?php echo App::getCurrentRoute(); ?>/charge/<?php echo $charge->id; ?>/delete">x</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo View::formatDate($charge->date); ?></td>
                        <td><?php echo $charge->name; ?></td>
                        <td class="amount"><?php echo View::formatAmount($charge->amount); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <td colspan="3">Total</td>
                        <td><?php echo View::formatAmount($total); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <table class="prestations">
            <thead>
                <tr>
                    <th colspan="4">Extras</th>
                </tr>
            </thead>
            <tbody>
                <?php if(is_array(View::data('extras'))): ?>
                    <?php $total = 0; ?>
                    <?php foreach(View::data('extras') as $extra): ?>
                    <?php $total += $extra->amount; ?>
                    <tr>
                        <td>
                            <?php if(!App::isLoggedUserAdmin()): ?>
                            <a class="delete" href="/<?php echo App::getCurrentRoute(); ?>/charge/<?php echo $extra->id; ?>/delete">x</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo View::formatDate($extra->date); ?></td>
                        <td><?php echo $extra->name; ?></td>
                        <td class="amount"><?php echo View::formatAmount($extra->amount); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <td colspan="3">Total</td>
                        <td><?php echo View::formatAmount($total); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>
