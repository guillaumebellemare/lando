Lando
=====
<p>Lando Calrissian. C’est un petit magouilleur, un combinard... un vaurien, il va vous plaire.</p>
=====
<p>http://guillaumebellemare.github.io/lando/</p>
=====
<p>https://www.youtube.com/watch?v=WrvHMW4rviE</p>
# Documentation
## Queries
### Select
```php
$this->select($this->table)</p>
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
