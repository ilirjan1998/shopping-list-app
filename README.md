# Shopping List App (Symfony REST API)

Eine kleine REST API und Weboberfläche zur Verwaltung von Einkaufslisten.

Das Projekt wurde mit **Symfony** entwickelt und speichert Daten in einer **MySQL Datenbank**.

---

## Features

* Einkaufslisten erstellen
* Items zu einer Liste hinzufügen
* Alle Items einer Liste anzeigen
* Einzelne Items anzeigen
* Items aktualisieren
* Items löschen
* Einkaufslisten löschen

--- 

## Screenshot

![App Screenshot](einkaufsliste.png)
![App Screenshot](localhost8000lists.png)

---

## Technologien 

* PHP
* Symfony
* Doctrine ORM
* MySQL
* Twig
* JavaScript (Fetch API)
* XAMPP

---

## Installation

Repository klonen:

```
git clone https://github.com/ilirjan1998/shopping-list-app.git
cd shopping-list-app
```

Dependencies installieren:

```
composer install
```

### XAMPP starten

Apache und MySQL im XAMPP Control Panel starten.


Datenbank konfigurieren in:

```
.env
```

Beispiel:

```
DATABASE_URL="mysql://root:password@127.0.0.1:3306/shopping_list_app"
```

Datenbank erstellen:

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

## Server starten

```
php -S localhost:8000 -t public
```

Danach im Browser öffnen:

```
http://localhost:8000/
```

---

## API Endpoints

### Create list

POST `/lists`

### Add item

POST `/lists/{id}/item`

### Get all items

GET `/lists/{id}/items`

### Get single item

GET `/lists/{id}/items/{itemId}`

### Update item

PUT `/lists/{id}/items/{itemId}`

### Delete item

DELETE `/lists/{id}/items/{itemId}`

### Delete list

DELETE `/lists/{id}`

---

## Datenbank Struktur

### shopping_list

| Feld | Typ     |
| ---- | ------- |
| id   | int     |
| name | varchar |

### item

| Feld             | Typ     |
| ---------------- | ------- |
| id               | int     |
| name             | varchar |
| quantity         | int     |
| shopping_list_id | int     |

---

## Weboberfläche

Die Weboberfläche befindet sich unter:

```
/
```

Dort können Listen erstellt, Items hinzugefügt, bearbeitet und gelöscht werden.

---
