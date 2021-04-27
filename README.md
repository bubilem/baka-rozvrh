# Webový školní rozvrh hodin

Tato webová aplikace si sama stahuje aktuální rozvrh v podobě XML souboru ze serveru s informačním systémem Bakaláři. Z tohoto týdenního XML si udělá denní JSON, ze kterého generuje HTML rozvrh.
Klient, kde se rozvrh zobrazuje se sám opakovaně dotazuje serveru, je-li něco nového. Server si při tomto dotazu sím zjistí, kdy a co je potřeba aktualizovat. Pokud je změna v rozvrhu, tak si server vše sám obstará a klientovi změnu pošle. Není tedy potřeba vytvářet paralelní aktualizaci dat pomocí cron nebo Task Manageru.

Demo v běhu nyní na adrese [baka-rozvrh.bubileg.cz](http://baka-rozvrh.bubileg.cz/rozvrh.html?section=marvdf).

## Použité technologie

- HTML, CSS, SASS (SCSS), PHP, JavaScript, JSON, XML

## Software na serveru

- Webový server Apache s PHP 7.
- Toť vše. Není potřeba žádný DBMS...

## Software na klientovi

- Webový browser. Testováno v Google Chrome.
