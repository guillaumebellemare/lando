# Lando
<p>Lando Calrissian. C’est un petit magouilleur, un combinard... un vaurien, il va vous plaire.<br>http://guillaumebellemare.github.io/lando/<br>https://www.youtube.com/watch?v=WrvHMW4rviE</p>
=====
# Documentation
## Controllers
```php
class IndexController extends AppController {

	function index() {
		
		global $lang2, $lang3;
		
		# Model declaration
		foo = new Foo();
		
		# Slug creation
		$foo->create_slug_field("bar", "name_$lang3", "slug_$lang3", "URL Slug - $lang2");
		
		# Data
		$foos = foo->getAllBar();
		$current_foo = current(foo->currentFoo());
		
		return array("foos" => $foos, "current_foo" => $current_foo);
	}
	
}
```
## Queries
### Select
```php
$this->select($this->table)->all()
```
### Join
```php
public function foo() {
  $this->select($this->table)->left_join("table_name")->all()
}

public function bar() {
  return $this->oneToMany("$this->table.id", "bar.foo_id");
}
```
### Where
```php
$this->select($this->table)->where("$this->table.id = 1")->all()
```
### Order By
```php
$this->select($this->table)->order_by("$this->table.id ASC")->all()
```
### Group By
```php
$this->select($this->table)->group_by("$this->table.id")->all()
```
### Append
```php
$this->select($this->table)->append(array("(DATE(SUBSTRING_INDEX($this->table.date_activation, ',', -1)) - INTERVAL 1 MONTH) AS special_date_renew", "DATE(SUBSTRING_INDEX($this->table.date_activation, ',', -1)) AS special_date_end"))->all()
```
### Get
```php
$this->get($id)
```
### Limit
```php
$this->select($this->table)->limit(1)
```
### Raw Query
```php
$this->raw_query($q)
```
### Insert
```php
$this->insert($record)
```
### Update
```php
$this->update($record, $id)
```
### Delete
```php
$this->delete($this->table, "id = $id")
```
### Complex Query
```php
$rs = $this->select($this->table)->all()
$bar = new Bar()

foreach($rs as $key => &$record)
{
	$record["bars"] = $bar->select($bar->table)->all()
}

return $rs
```
=====
## Functions
### Create Slug Field
```php
$foo->create_slug_field("bar", "name_$lang3", "slug_$lang3", "URL Slug - $lang2");
```
### Redirect
```php
this->redirect("route_to_redirect");
```
=====
## View Functions
### Read returned array
```php
<?php foreach($foo as $bar) : ?>
	<?=$bar["bars.name_$lang3"]?>
<?php endforeach; ?>
```
### Write Pretty Date
```php
$app->writePrettyDate("$date")
```
### Write Pretty Month
```php
$app->writePrettyMonth("$month")
```
### Limit String Size
```php
$app->limitStringSize($month, 200)
```
### Format Money
```php
$app->format_money($price)
```
### Get picture path
```php
$app->getPicturePath($picture_path)
```
### Get picture infos
```php
$app->getPictureInfo($picture_path)
```
### New line to paragraph
```php
$app->nl2p(string)
```
=====
## Routes
<p>You have to set your routes in two differents files:</p>
### public/lang/routes.php
```php
$routes = array(
	"index" => "index",
);
```
### app/core/app_routes.php
```php
$app_routes = array(
	"index" => "IndexController@index",
);
```
#### Password Protected
```php
$app_routes = array(
	"index" => "IndexController@index::protected",
);
```
=====
## Metas
```php
$this->setTitle("Title");
$this->setDescription("Description");
$this->setKeywords("key, words");
$this->setPageType("article");
$this->setImage("image.jpg");
```
