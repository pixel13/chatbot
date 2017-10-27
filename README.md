Semplice programma PHP per costruire un chatbot

Il Chatbot si basa su una serie di regole definite in un file json

Tutte le sezioni del json sono opzionali, naturalmente più ce ne sono più il comportamento del chatbot sarà complesso.

L'ordinamento delle sezioni nel json è importante perché ogni sezione genera una regola e la prima regola che risponde correttamente a un certo messaggio è quella che viene utilizzata per rispondere.
Quindi è bene che le risposte generiche siano in fondo e che l'ultima sia sempre la sezione unknown.

Start e end devono essere in cima, se specificate.

Possono essere utilizzate delle espressioni regolari nella sezione answers

Le variabili sono utili per variare alcuni parametri

Ogni messaggio o variabile è associato a una stringa corrispondente alla probabilità di far uscire quel valore (numero da 0 a 100) che può essere anche uguale a stringa vuota se si vuole che le probabilità siano distribuite equamente.
Se in un gruppo alcuni elementi hanno specificata una probabilità fissa e altri non hanno specificata alcuna probabilità, questi ultimi si divideranno equamente la probabilità rimanente.

Quando nessuna regola fa match il bot non sa cosa rispondere e quindi inserirà "..." come risposta.

Da fare: quando si richiama l'API in get, se c'è un messaggio disponibile lo prende.

Nella definizione delle probabilità, il sistema deve coprire comunque il range 1-100, quindi:
- se c'è una sola opzione vale comunque 100 (il valore originario viene ignorato, qualunque sia)
- se ci sono opzioni con probabilità non specificata (stringa vuota) il sistema distribuisce equamente i valori mancanti per arrivare almeno a 100 (può superare 100 per via degli arrotondamenti, quindi una opzione può essere sfavorita, solitamente l'ultima)
- se ci sono opzioni che hanno come probabilità 0 o un valore non interpretabile come intero il sistema le esclude
- se tutte le probabilità sono definite ma la somma è inferiore a 100, il sistema aumenta in maniera equa tutte le probabilità di tutte le opzioni per raggiungere almeno 100 (in questo caso anche delle eventuali opzioni a 0, che quindi diventano selezionabili)

Sarebbe bene che ci fosse sempre una sezione topic per far partire argomenti e non essere sempre un domanda-risposta. Di sicuro se c'è change ci deve essere topics.

In tutte le regole sarebbe figo se ci fosse un controllo della history per evitare ripetizioni, magari modificando anche le probabilità al volo.

Le options non sono obbligatorie, consentono di controllare il funzionamento di alcune regole, in particolare:
- change_frequency: indica la probabilità (valore da 0 a 100) che venga applicata la regola del cambio di argomento (default 10)
- recurring_frequency: indica la probabilità (valore da 0 a 100) che venga applicata la regola dell'argomento ricorrente (default 10)

Le options sono copiate sul frontend e guidano anche alcuni comportamenti che partono dal frontend come wait_to_talk

Id e name ci devono essere sempre nella configuration