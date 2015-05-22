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
<p>$this->select($this->table)</p>
<h3>Join</h3>
```php
public function foo() {
  $this->select($this->table)->left_join("table_name")
}

public function table_name() {
  return $this->oneToMany("$this->table.id", "table_name.table_name_id");
}
```
