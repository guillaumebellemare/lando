# Lando
=====
Lando Calrissian. C’est un petit magouilleur, un combinard... un vaurien, il va vous plaire.
=====
http://guillaumebellemare.github.io/lando/
=====
https://www.youtube.com/watch?v=WrvHMW4rviE
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
