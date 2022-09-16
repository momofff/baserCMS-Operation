window.onload = function() {
  let radios = document.getElementsByName('data[Operation][user_group][all]');
  let checkboxGroup = document.querySelector('ul.op-checkbox-group-any');

  radios.forEach(
    radio => {
      if (radio.checked == true) switchCheckboxGroup(radio.value);
      radio.addEventListener('change', e => {
        switchCheckboxGroup(e.target.value);
      })
    }
  );

  function switchCheckboxGroup(value) {
    if (value == 'ALL') {
      checkboxGroup.dataset.bcaState = '';
    } else {
      checkboxGroup.dataset.bcaState = 'open';
    }
  }
}
