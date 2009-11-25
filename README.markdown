Lisphp
======

Lisphp is a Lisp dialect written in PHP. It purposes to be embedded in web
applications to be distributed or web services and so implements sandbox
environment for security issues and multiple environment instances.


Standalone command line interface
---------------------------------

There is `lis.php`, a standalone command line interface. It takes a parameter,
a program filename to execute.

    $ php lis.php program.lisphp

You can run programs in sandbox with an option `-s`.

    $ php lis.php -s program.lisphp


REPL
----

If there is no filename in arguments to `lis.php`, it enters REPL mode.

    $ php lis.php
    >>> (form to evaulate)

Similarly you can specify `-s` to restrict it sandbox.

    $ php -s lis.php

 - `>>>` is a prompt.
 - `==>` is a returned value of evaluation.
 - `!!!` is a thrown exception.


Embed in your app
-----------------

In order to execute the Lisphp program, an _environment instance_ is required.
Environment means global state for the program. It includes global symbols,
built-in functions and macros. A program to execute starts from the initialized
environment. You can initialize an environment with `Lisphp_Environment` class.

    require_once 'Lisphp.php';
    $env = Lisphp_Environment::sandbox();
    $program = new Lisphp_Program($lisphpCode);
    $program->execute($env);

There are two given environment sets in `Lisphp_Environment`. One is the
sandbox, restricted to inside of Lisphp, which is created with the method
`Lisphp_Environment::sandbox()`. It cannot touch outside of Lisphp, PHP side.
Programs cannot access to file system, IO, etc. The other is the full
environment of Lisphp, which is initialized with `Lisphp_Environment::full()`.
In the environment, `use` macro, is for importing native PHP functions and
classes, is provided. File system, IO, socket, et cetera can be accessed in
the full environment. Following code touches file a.txt and writes some text.

    (use fopen fwrite fclose)

    {let [fp (fopen "a.txt" "w")]
         (fwrite fp "some text")
         (flose fp)}


Macro `use`
-----------

The full environment of Lisphp provides `use` macro. It can import native
PHP functions and classes.

    (use strrev array_sum array-product [substr substring])

It takes function identifiers to import. Hyphens in identifiers are replaced to
underscores. Lists that contain two symbols are aliasing.

    (strrev "hello")                #=> "olleh"
    (array_sum [array 1 2 3])       #=> 6
    (array-product [array 4 5 6])   #=> 120
    (substring "world" 2)           #=> "rld"

Wrap identifiers with angle brackets in order to import class. According
[PEAR naming convention][1] for classes, slashes are treated as hierarchical
separators, so replaced to underscores.

    (use <PDO> Lisphp/<Program>)

Imported classes are applicable. They as functions behave as instantiation.
Static methods in imported classes are also imported.

    (<PDO> "mysql:dbname=testdb;host=127.0.0.1" "dbuser" "dbpass")
    (Lisphp/<Program>/load "program.lisphp")

 [1]: http://pear.php.net/manual/en/standards.naming.php


About lists and `nil`
---------------------

Lisphp implements lists in primitive, but it has some differences between
original Lisp. In original Lisp, lists are made by [cons][] pairs. But lists in
Lisphp is just instance of `Lisphp_List` class, a subclass of
[`ArrayObject`][arrayobject]. So exactly it is not linked list but similar to
array. In like manner `nil` also is not empty list in Lisphp unlike in original
Lisp. It is a just synonym for PHP [`null`][null] value.


 [cons]: http://en.wikipedia.org/wiki/Cons
 [arrayobject]: http://php.net/manual/en/class.arrayobject.php
 [null]: http://php.net/manual/en/language.types.null.php


Author and license
------------------

The author is Hong, MinHee <http://dahlia.kr/>.

Lisphp is distributed under MIT license.
