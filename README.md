# Einkaufslisten App

Eine kleine REST API und Weboberfläche zur Verwaltung von Einkaufslisten.

Das Projekt wurde mit **Symfony** entwickelt und speichert Daten in einer **MySQL Datenbank**.

---

# Features

* Einkaufslisten erstellen
* Items zu einer Liste hinzufügen
* Alle Items einer Liste anzeigen
* Einzelne Items anzeigen
* Items aktualisieren
* Items löschen
* Einkaufslisten löschen

Zusätzlich gibt es eine einfache Weboberfläche unter:

```
http://localhost:8000/app

```

## Screenshot

![App Screenshot](einkaufliste.png)

---

# Technologien

* PHP
* Symfony
* Doctrine ORM
* MySQL
* Twig
* JavaScript (Fetch API)

---

# Installation

Repository klonen:

```
git clone <repository-url>
cd shopping-list-app
```

Dependencies installieren:

```
composer install
```

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

# Server starten

```
php -S localhost:8000 -t public
```

Danach im Browser öffnen:

```
http://localhost:8000/app
```

---

# API Endpoints

## Liste erstellen

POST

```
/lists
```

Body Beispiel:

```
{
  "name": "Supermarkt",
  "items": [
    { "name": "Banane", "quantity": 4 },
    { "name": "Milch", "quantity": 2 }
  ]
}
```

---

## Item hinzufügen

POST

```
/lists/{id}/item
```

---

## Alle Items einer Liste

GET

```
/lists/{id}/items
```

---

## Einzelnes Item anzeigen

GET

```
/lists/{id}/items/{itemId}
```

---

## Item aktualisieren

PUT

```
/lists/{id}/items/{itemId}
```

---

## Item löschen

DELETE

```
/lists/{id}/items/{itemId}
```

---

## Liste löschen

DELETE

```
/lists/{id}
```

---

# Datenbank Struktur

## shopping_list

| Feld | Typ     |
| ---- | ------- |
| id   | int     |
| name | varchar |

## item

| Feld             | Typ     |
| ---------------- | ------- |
| id               | int     |
| name             | varchar |
| quantity         | int     |
| shopping_list_id | int     |

---

# Weboberfläche

Die Weboberfläche befindet sich unter:

```
/app
```

Dort können Listen erstellt, Items hinzugefügt und gelöscht werden.

---

