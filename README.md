# WEB APP CAMPUS_SPORT
Progetto per il corso tecnologie Web dell'Università di Bologna a.a. 25/26.

L'applicazione offre un servizio di prenotazione campi per qualsiasi genere di sport per gli studenti del campus di Cesena.
Ogni studente avrà modo di vedere le proprie prenotazioni: passate, presenti e future; oltre alla possibilità di segnalare qualsiasi problema si incorra con i campi o con altri studenti

# Come avviare
## Deploy via Xampp.
Per costruire correttamento il db seguire questi passaggi:

* installare Xampp
* aprire porta libera
* avviare server
* localizzare cartella htdocs(all'interno della cartella Xampp)
* copiare all'interno di htdocs il repository
* andare su http://localhost/phpmyadmin/, se vi fa vedere la dashboard allora il server è avviato corrrettamente in locale
* una volta sulla dashboard andare sulla finestra SQL e copiare interamente il codice di database/schema.sql
* creato lo schema eseguite nel seguente ordine anche data.sql, data_extra.sql e gli altri file in qualsiasi ordine
* infine andare su http://localhost/campus_sport/index.php

