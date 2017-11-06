Chatbot
=======

**Chatbot** è un semplice programma in PHP per la costruzione di un chatbot.
Non pretende di avere alcuno scopo pratico, è stato costruito per puro divertimento.

Il Chatbot si basa su una serie di regole definite in un file json, che deve trovarsi nella folder "config".
Il nome del file di configurazione corrisponde all'identificativo (*id*) del chatbot.
Questo *id* deve essere passato in GET alla pagina della chat per invocare un certo chatbot: utilizzando la pagina index
questa operazione viene effettuata automaticamente semplicemente scegliendo una voce dalla tendina.


Il file json di configurazione
------------------------------

Il file presenta una serie di voci, tra le quali le uniche obbligatorie sono *id* e *name*: tutte le altre, pur non essendo necessarie, servono ad incrementare le risposte del bot.

Nel dettaglio, le voci che possono essere inserite nel file json sono:

- *id* (obbligatorio): identificativo del bot, corrisponde al nome del file (senza l'estensione json)
- *name* (obbligatorio): nome comune del bot, viene mostrato all'utente
- *avatar*: nome del file di immagine che dovrà fungere da avatar del bot, dovrà trovarsi nella folder public/img
- *mood*: frase che indica il mood del bot, se presente viene stampata nella pagina della chat
- *options*: opzioni delle varie regole (vedi più avanti)
- *variables*: variabili da sostituire all'interno delle regole (vedi più avanti)
- segue poi la sezione delle regole: ogni regola ha un nome, le regole disponibili sono:
    - *pattern*
    - *start*
    - *end*
    - *recurring*
    - *topics*
    - *change*
    - *answers*
    - *generic*
    - *unknown*

Si può scegliere se includere tutte o solo alcune delle regole nella configurazione del bot.
Le regole vengono applicate nell'ordine con il quale sono riportate nel json, quindi conviene rispettare l'ordine dato sopra
perché introduce una logica (e.g. la regola unknown che si attiva sempre è bene che sia l'ultima, altrimenti ogni altra
regola che venisse dopo sarebbe ignorata).


Le percentuali di probabilità
-----------------------------

Il bot utilizza una serie di regole e ognuna di queste regole prevede una serie di risposte, tra le quali viene scelta
quella da dare in base a un numero casuale tra 1 e 100. In tutte le regole, infatti, è previsto
l'assegnamento ad ogni risposta di un valore percentuale (che indicheremo di seguito con <PERC>) ovvero di un valore
da 0 a 100 (0 equivale a disabilitare una riposta). Maggiore è il valore, maggiore sarà la probabilità che una determinata risposta venga selezionata.

In teoria la somma dei valori percentuali di tutte le risposte disponibili per una certa regola dovrebbe dare 100,
in pratica anche se il valore supera questa soglia, verranno presi in considerazione i primi 100 valor disponibili.
Se la somma delle percentuali di tutte le risposte fosse invece inferiore a 100, la differenza verrà spalmata in maniera
equa su tutte le risposte disponibili (anche su quelle con valore 0 in questo caso).

Se alcune risposte non hanno un valore percentuale specificato (ovvero come valore hanno stringa vuota), il sistema automaticamente suddividerà le percentuali
non assegnate suddividendole in maniera equa tra le risposte.

Vediamo alcuni esempi di normalizzazione delle percentuali (per brevità rappresentiamo con le lettere delle possibili risposte e dopo il simbolo => la percentuale associata):

`A => "", B => "", C => "", D => ""` viene normalizzato in `A => "25", B => "25", C => "25", D => "25"`
`A => "50", B => "", C => ""` viene normalizzato in `A => "50", B => "25", C => "25"`
`A => "10", B => "50"` viene normalizzato in `A => "30", B => "70"`
`A => "80", B => "25", C => "15"` viene normalizzato in `A => "80", B => "20", C => "0"`



Le regole
---------

Il funzionamento del bot si basa su una serie di regole che vengono attivate dal messaggio postato dall'utente (sono
cioè tutte regole *in risposta* a un messaggio di un utente), tuttavia in alcuni casi possono reagire anche a un
messaggio vuoto.

Vediamo nel dettaglio ogni singola regola:


### Pattern

E' la regola per eccellenza del chatbot: quando il messaggio dell'utente soddisfa una certa espressione regolare,
il bot fornisce una delle risposte associate. Nel file json, quindi, per la regola *pattern* sarà specificata una configurazione
del tipo:

`"pattern" : {
    "<REGEXP1>" : { "<ANSWER1>" : "<PERC>", "<ANSWER2>" : "<PERC>", ... },
    "<REGEXP2>" : { "<ANSWER1>" : "<PERC>", "<ANSWER2>" : "<PERC>", ... },
    ...
},`

Trattandosi di una regola che risponde a un messaggio in maniera precisa, è bene che sia la prima delle regole disponibili, nell'ordine.



### Start

E' la regola per definire il primo messaggio che il bot mette nella chat (solitamente un saluto).
Nel file json per questa regola sono presenti le risposte disponibili.
Anche questa regola risponde a un'esigenza molto precisa quindi è bene che sia una delle prime.



### End

Questa regola specifica le situazioni in cui il bot chiude la conversazione.
Nel file json per questa regola sono presenti le frasi che utilizza per chiudere la conversazione.
Questa regola può avere poi anche una sezione tra le °options*, chiamata *end_frequency*, che indica in quali
occasioni il bot chiude la conversazione: si possono specificare tre valori:
    - *own_messages*: la conversazione viene chiusa quando gli ultimi X messaggi della chat sono tutti del bot (in pratica l'utente non sta rispondendo), con il valore 0 questo meccanismo viene disabilitato (default: 5)
    - *seconds*: la conversazione viene interrotta dopo X secondi dall'inizio, con il valore 0 questo meccanismo viene disabilitato (default: 0)
    - *random*: in maniera randomica, con probabilità X, la conversazione viene interrotta; con il valore 0 questo meccanismo viene disabilitato (default: 0)
Se la regola è presente nell'elenco delle regole, ma le opzioni non sono specificate, venono applicate quelle di default.



### Recurring

Lo scopo di questa regola è fornire delle risposte ricorrenti.
Nel json per questa regola vengono indicate le risposte ricorrenti disponibili.
Tra le opzioni può essere specificata una voce chiamata *recurring_frequency* che indica la probabilità che la regola venga applicata.
Se la regola è presente ma non viene specificata l'opzione corrispondente, viene applicato il valore di default, pari a 10%.



### Topics

Questa regola è pensata con lo scopo di lanciare dei nuovi argomenti, quindi in pratica è l'unica regola che non risponde
a un messaggio inviato dall'utente (in realtà tecnicamente risponde a un messaggio vuoto).
Nel file json per questa regola sono presenti le diverse frasi che il bot può usare per iniziare un nuovo argomento.



### Change

Strettamente correlata alla precedente, la regola *change* consente al bot di cambiare argomento.
Nel json per questa regola sono presenti frasi che indicano un brusco cambio di argomento da parte del bot (e.g.
"perché invece non parliamo di un'altra cosa?" e simili). Quando viene applicata questa regola, viene immediatamente
generato un nuovo messaggio da parte del bot (e questo in pratica invoca la regola °topics*).
Tra le opzioni è possibile inserire una voce chiamata *change_frequency* che indica la probabilità che questa regola venga applicata.
Se l'opzione corrispondente non viene specificata, la regola viene applicata con una probabilità di default pari al 10%.



### Answers

Questa regola viene applicata in risposta a domande effettuate dall'utente (in pratica, frasi che terminano col punto interrogativo).
Nel json per questa regola dovrebbero esserci quindi delle risposte generiche (quelle più specifiche saranno nella regola *patterns*)
al solo scopo di fornire una risposta verosimile (dato che solitamente la risposta a una domanda diretta ha una struttura
diversa dalle altre frasi).



### Generic

Questa regola dovrebbe trovarsi verso il basso (ultima o penultima, nel caso sia presente *unknown*) e serve a fornire
delle risposte generiche, quando ogni altra regola non è scattata.
Nel json per questa regola ci saranno quindi delle risposte molto vaghe e indefinite.
Tra le opzioni invece è possibile inserire una sezione chiamata *generic_apply* che specifica in quali casi questa regola viene applicata.
In particolare è possibile specificare un valore di true o false per il valore regexp, che indica se deve essere applicata o meno
una espressione regolare sperimentale che dovrebbe servire a capire quando la frase fornita dall'utente è ben formata in italiano (in tal modo
se la frase non è ben formata - ad esempio se l'utente ha digitato lettere a caso sulla tastiera - il bot non fornisce uan risposta generica
ma passa alla regola successiva). L'altro parametro che è possibile specificare è una *frequency* intesa come probabilità.
Dato lo scopo di questa regola, questo valore dovrebbe essere sempre abbastanza alto (di default è 90 e la regexp viene applicata).



### Unknown

E' una regola che viene applicata sempre, quindi è bene che sia l'ultima della catena. Serve a dare una o più risposte che indicano
un messaggio che il bot non è stato in grado di interpretare. In mancanza di questa regola, il bot potrà dare in questi casi
come risposta tre puntini di sospensione.



Variabili
---------

In tutte le frasi di ogni regola è possibile specificare delle variabili utilizzando la sintassi `${NOMEVARIABILE}`
Questo placeholder verrà sostituito con uno dei valori specificati per NOMEVARIABILE all'interno della sezione
*variables* del json. Per ogni variabile ha senso che ci siano almeno due valori; anche qui ogni valore sarà associato
a una probabilità e il sistema sceglierà in modo simile alla selezione delle risposte.