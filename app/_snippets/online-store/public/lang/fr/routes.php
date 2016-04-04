<?php

$routes = array(
	"cart" => "mon-panier",
	"cart_upload-logo" => "mon-panier/televerser-un-logo/{$_GET['param2']}",
	"cart_delete-logo" => "mon-panier/effacer-le-logo",
	"cart_update" => "mon-panier/mise-a-jour",
	"cart_remove" => "mon-panier/retirer",
	"cart_add" => "mon-panier/ajouter",
	"login" => "connexion",
	"checkout" => "commande/adresse-dexpedition",
	"checkout_login" => "commande/connexion",
	"checkout_billing-address" => "commande/adresse-de-facturation",
	"checkout_order-confirmation" => "commande/confirmation-de-votre-commande",
	"checkout_completed-transaction" => "commande/transaction-completee",
	"forgot" => "recuperation-de-votre-mot-de-passe",
	"recover" => "reinitialisation-de-votre-mot-de-passe",
	"recover_valid" => "reinitialisation-de-votre-mot-de-passe/{$_GET['param1']}/{$_GET['param2']}",
);
