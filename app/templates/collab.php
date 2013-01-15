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
            $totalToRempla += $recap['total_to_collab'];
            $totalPaid += $recap['total_paid'];
        ?>
        <tr>
            <td class="label"><?php echo $name; ?></td>
            <td><?php echo View::formatAmount($recap['total_real']); ?></td>
            <td class="label"><?php echo $name; ?></td>
            <td><?php echo View::formatAmount($recap['total_to_collab']); ?></td>
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
    
    <div id="who">
        <?php foreach(View::data('collabs') as $collab): ?>
        <a href="/collab/<?php echo $collab->id; ?>" <?php echo ($collab->id == View::data('current')->id) ? ' class="current"' : ''; ?>><?php echo $collab->name; ?></a>
        <?php endforeach; ?>
    </div>
    <form method="post">
        <?php if(!App::isLoggedUserAdmin()): ?>
        <div id="add-row-content" class="collab-row">
            <label>Date</label>
            <input type="text" id="date" name="date" maxlength="10" value="<?php echo (View::data('date')) ? View::data('date') : date('d/m/Y'); ?>" />
            <span class="sep">|</span>
            <input type="text" id="patient-name" maxlength="30" name="patient_name" value="<?php echo (View::data('patient_name')) ? View::data('patient_name') : 'Nom du patient'; ?>" />
            <input type="text" id="amount" name="amount" value="<?php echo (View::data('amount')) ? View::data('amount') : 'Montant'; ?>" />
            <input type="submit" id="add-presta-but" name="add_prestation" value="Prestation" />
            <span class="sep">OU</span>
            <select name="percent">
                <?php foreach(View::data('percents') as $percent): ?>
                <option value="<?php echo $percent; ?>" <?php echo ($percent == View::data('percent')) ? 'selected="selected"' : ''; ?>><?php echo $percent; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if(View::data('current')->is_collab_assoc_tier_pay): ?>
            <input type="submit" id="add-payment-mutuel-but" name="add_payment_mutuel" value="Mutuelle" />
            <input type="submit" id="add-payment-cpam-but" name="add_payment_cpam" value="CPAM" />
            <?php else: ?>
            <input type="submit" id="add-payment-but" name="add_payment" value="Paiement" />
            <?php endif; ?>
        </div>
        <?php endif; ?>
    
        <div class="clearfix">
            <?php 
                $types = array();
                $types[] = array(
                    'class' => 'prestations-unpaid',
                    'title' => 'Prestations impayés',
                    'collab_list' => 'unpaid_prestations',
                    'show_checkbox' => true,
                    'show_delete' => true
                );
                $types[] = array(
                    'class' => 'prestations-paid',
                    'title' => 'Prestations payés du mois',
                    'collab_list' => 'paid_prestations',
                    'show_checkbox' => false,
                    'show_delete' => false
                );
            ?>
            <?php foreach($types as $type): ?>
            <?php $collabListKey = $type['collab_list']; ?>
            <table class="prestations <?php echo $type['class']; ?>">
                <thead>
                    <tr>
                        <th colspan="7"><?php echo $type['title']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(is_array(View::data('current')->$collabListKey)): ?>
                        <?php foreach(View::data('current')->$collabListKey as $prestation): ?>
                        <tr>
                            <td>
                                <?php if(!App::isLoggedUserAdmin() && $type['show_checkbox']): ?>
                                <input type="checkbox" name="prestations[]" value="<?php echo $prestation->id; ?>" />
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!App::isLoggedUserAdmin() && $type['show_checkbox']): ?>
                                <a class="delete" href="/<?php echo App::getCurrentRoute(); ?>/prestations/<?php echo $prestation->id; ?>/delete">x</a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo View::formatDate($prestation->date); ?></td>
                            <td><?php echo $prestation->patient_name; ?></td>
                            <td>
                                <?php if(View::data('current')->is_collab_assoc_tier_pay && $prestation->is_paid_mutuel): ?>
                                <span class="paid-tier-pay">M</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(View::data('current')->is_collab_assoc_tier_pay && $prestation->is_paid_cpam): ?>
                                <span class="paid-tier-pay">C</span>
                                <?php endif; ?>
                            </td>
                            <td class="amount"><?php echo View::formatAmount($prestation->amount); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endforeach; ?>
            <table class="prestations payments">
                <thead>
                    <tr>
                        <th colspan="5">Paiments du mois</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(is_array(View::data('current')->payments)): ?>
                        <?php $total = 0; ?>
                        <?php foreach(View::data('current')->payments as $payments): ?>
                        <?php $total += $payments->amount; ?>
                        <tr>
                            <td>
                                <?php if(!App::isLoggedUserAdmin()): ?>
                                <a class="delete" href="/<?php echo App::getCurrentRoute(); ?>/payment/<?php echo $payments->id; ?>/delete">x</a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo View::formatDate($payments->date); ?></td>
                            <td><?php echo $payments->patient_name; ?></td>
                            <td>
                            <?php 
                                if($payments->type != Payment::TYPE_FULL) {
                                    echo $payments->type;
                                }
                            ?>
                            </td>
                            <td class="amount"><?php echo View::formatAmount($payments->amount); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total">
                            <td colspan="4">Total</td>
                            <td><?php echo View::formatAmount($total); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>