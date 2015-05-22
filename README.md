Lando
=====
<p>Lando Calrissian. C’est un petit magouilleur, un combinard... un vaurien, il va vous plaire.</p>
=====
<p>http://guillaumebellemare.github.io/lando/</p>
=====
<p>https://www.youtube.com/watch?v=WrvHMW4rviE</p>
<h1>Documentation</h1>
<h2>Queries</h2>
<h3>Select</h3>
<p>$this->select($this->table)</p>
<h3>Join</h3>
<code>$this->select($this->table)->left_join("table_name")</code>
<br>
<code>public function table_name() { return $this->oneToMany("$this->table.id", "table_name.table_name_id"); }</code>
