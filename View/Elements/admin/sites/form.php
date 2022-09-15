    <tr>
      <th>
        <?php echo $this->BcForm->label('Operation.user_group', __d('baser', '管理ユーザーグループ')); ?>
      </th>
      <td>
        <?php echo $this->BcForm->input('Operation.user_group.all', $op['all_user_group']); ?>
        <?php echo $this->BcForm->error('Operation.user_group.all'); ?>
        <ul class="user_group_check_any">
          <?php foreach ($op['any_user_group'] as $i => $user_group) { ?>
          <li>
            <?php echo $this->BcForm->input($user_group['name'], $user_group['option']); ?>
            <?php echo $this->BcForm->label($user_group['name'], $user_group['label']); ?>
          </li>
          <?php } ?>
        </ul>
        <?php echo $this->BcForm->error('Operationi.user_group.any'); ?>
      </td>
    </tr>
