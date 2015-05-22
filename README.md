# Lando
<p>Lando Calrissian. C’est un petit magouilleur, un combinard... un vaurien, il va vous plaire.<br>http://guillaumebellemare.github.io/lando/<br>https://www.youtube.com/watch?v=WrvHMW4rviE</p>
=====
# Documentation
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
## Functions
### WritePrettyDate
```php
$app->writePrettyDate("$date")
```
