<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<h1>Elements</h1>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla enim dui, faucibus quis nibh in, imperdiet vehicula metus. Mauris eget purus gravida, iaculis nulla in, lacinia magna. Proin gravida, <a href="#">ipsum vitae fermentum</a> mollis, nisi sem faucibus leo, vitae sodales eros turpis quis erat. Donec lacinia erat tristique, pharetra erat at, finibus lectus.</p>
<p>Sed ut mattis nulla. Aliquam erat volutpat. Maecenas a dui ultricies justo euismod mattis. Aenean a leo vel tellus ullamcorper dignissim. Praesent pulvinar tristique orci non interdum. Aliquam ut finibus risus. Aenean volutpat convallis luctus. Fusce fermentum imperdiet lacus, sit amet posuere erat aliquam id. Sed eget urna nec tellus pellentesque pretium.</p>
<ul>
  <li>List
    <ul>
      <li>List</li>
      <li>List</li>
      <li>List</li>
      <li>List</li>
    </ul>
  </li>
  <li>List</li>
  <li>List</li>
  <li>List</li>
</ul>
<hr>
<h1>Buttons</h1>
<a href="#" class="btn has-blue-bg has-rounded-corners has-box-shadow">Button</a> <a href="#" class="btn has-red-bg has-rounded-corners has-box-shadow">Button</a> <a href="#" class="btn has-yellow-bg has-rounded-corners has-box-shadow">Button</a><a href="#" class="btn has-green-bg has-rounded-corners has-box-shadow">Button</a>
<hr>
<h1>Forms</h1>
<input name="" type="text" placeholder="Normal">
<input name="" type="text" class="is-error" placeholder="Error">
<input name="" type="text" class="is-successful" placeholder="Success">
<input name="" type="text" disabled placeholder="Inactive">
<textarea name="" placeholder="Normal"></textarea>
<textarea name="" class="is-error" placeholder="Error"></textarea>
<textarea name="" class="is-successful" placeholder="Success"></textarea>
<textarea name="" disabled placeholder="Inactive"></textarea>
<select name="sSelect">
  <option value="1">Select #1</option>
  <option value="2">Select #2</option>
  <option value="3">Select #3</option>
  <option value="4">Select #4</option>
</select>
<input type="radio" name="rRadio" value="0" checked id="rRadio_0">
<label for="rRadio_0">Radio 1</label>
<input type="radio" name="rRadio" value="1" id="rRadio_1">
<label for="rRadio_1">Radio 2</label>
<input type="radio" name="rRadio" value="2" id="rRadio_2">
<label for="rRadio_2">Radio 3</label>
<input type="checkbox" name="cCheck" checked value="0" id="cCheck_0">
<label for="cCheck_0">Checkbox 1</label>
<input type="checkbox" name="cCheck" value="1" id="cCheck_1">
<label for="cCheck_1">Checkbox 2</label>
<input type="checkbox" name="cCheck" value="2" id="cCheck_2">
<label for="cCheck_2">Checkbox 3</label>
<input name="" type="submit" class="btn has-green-bg" value="Button">
<hr>
<h1>Tables</h1>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th>Row</th>
    <th>Row</th>
    <th>Row</th>
  </tr>
  <tr>
    <td>Row</td>
    <td>Row</td>
    <td>Row</td>
  </tr>
  <tr>
    <td>Row</td>
    <td>Row</td>
    <td>Row</td>
  </tr>
</table>
<?php endblock() ?>
<?php startblock('sidebar') ?>
<h1>Sidebar</h1>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/flexslider.php')); ?>
<?php endblock() ?>
