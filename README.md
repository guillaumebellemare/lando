# Lando
<p>Lando Calrissian. C’est un petit magouilleur, un combinard... un vaurien, il va vous plaire.<br>http://guillaumebellemare.github.io/lando/<br>https://www.youtube.com/watch?v=WrvHMW4rviE</p>
=====
# Documentation
## Queries
### Select
```php
$this->select($this->table)
```
### Join
```php
public function foo() {
  $this->select($this->table)->left_join("table_name")
}

public function bar() {
  return $this->oneToMany("$this->table.id", "bar.foo_id");
}
```
### Where
```php
$this->select($this->table)->where("$this->table.id = 1")
```
### Order By
```php
$this->select($this->table)->order_by("$this->table.id ASC")
```
### Group By
```php
$this->select($this->table)->group_by("$this->table.id")
```
### Append
```php
$this->select($this->table)->append(array("(DATE(SUBSTRING_INDEX($this->table.date_activation, ',', -1)) - INTERVAL 1 MONTH) AS special_date_renew", "DATE(SUBSTRING_INDEX($this->table.date_activation, ',', -1)) AS special_date_end"))
```
