    Salutare,
    Acesta este testul hero realizat.
    Presupun ca se stiu proceduri standard, precum git clone, composer install, dar, de dragul readme


Pentru a instala proiectul sunt necesari urmatori pasi:
1. se descarca fie prin comanda git clone https://github.com/TheMircea/test_hero.git fie prin zip si dezarhivare acest repositori
2. se deschide folderul unde se afla proiectul si se ruleaa comanda "composer install" sau "php composer.phar install" daca acesta nu este instalat global
3. in radacina proectului se regaseste un dump.sql, creati o baza de date mysql in care sa importati acest dump, acesta contine tabelele necesare functionarii aplicatiei.
4. Tot in radacina, se regaseste un fisier .env.dist acesta este un exemplu de fisier ce contin configuratiile aplicatiei cu exemple. Copiati acest fisier si redenumit-l .env
5. in fisierul .env configurati hostul, userul si parola la baaz de date, cat si base url-ul daca este cazul (xampp, unicontroller etc)