    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcForm->label('Operation.user_group', __d('baser', '管理ユーザーグループ')); ?>
      </th>
      <td class="bca-form-table__input">
        <?php echo $this->BcForm->input('Operation.user_group.all', $op['all_user_group']); ?>
        <?php echo $this->BcForm->error('Operation.user_group.all'); ?>
        <ul class="op-checkbox-group-any" data-bca-state="open">
          <?php foreach ($op['any_user_group'] as $i => $user_group) { ?>
          <li><?php echo $this->BcForm->input($user_group['name'], $user_group['option']); ?></li>
          <?php } ?>
        </ul>
        <?php echo $this->BcForm->error('Operation.user_group.any'); ?>
      </td>
    </tr>
