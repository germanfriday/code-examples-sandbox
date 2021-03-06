help/embargo.php                                                                                    0000644 0000765 0000765 00000000647 10251663151 014720  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        <b>Embargar un mensaje</b>
<p>Puede embargar un mensaje para que no se env&iacute;e hasta la fecha y hora que especifique. El valor por defecto es hoy a las 0:00, de modo que el mensaje se env&iacute;a inmediatamente.</p>
<p><b>Nota</b>: el embargo determina el momento en que comenzar&aacute; el env&iacute;o del mensaje. Esto no significa que los mensajes llegar&aacute;n a los usuarios precisamente en ese momento.
</p>
                                                                                         help/format.php                                                                                     0000644 0000765 0000765 00000004442 10251663151 014571  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        <h3>Formato del mensaje</h3>
Si utiliza &#171;Auto detectar&#187; el mensaje ser&aacute; considerado HTML en cuanto se encuentre la primera etiqueta HTML (&lt; ... &gt;).
</p><p><b>No hay problema en dejar seleccionado &#171;Auto detectar&#187;.</b></p><p>
Si no est&aacute; seguro de si &#171;Auto detectar&#187; funciona, y el mensaje que est&aacute; pegando ha sido formateado en HTML, escoja &#171;HTML&#187;.
Referencias a recursos externos (p. ej. im&aacute;genes) requieren la URL completa, es decir incluyendo el &#171;http://&#187; (al contrario que las im&aacute;gines de los patrones).
En todo lo dem&aacute;s, el formato del texto depende enteramente de usted.
<p>Si quiere que el mensaje se env&iacute;e a todos en texto plano, seleccione &#171;Texto&#187;.
</p><p>
Esta informaci&oacute;n se usa para crear la versi&oacute;n en texto plano de un correo HTML, o la versi&oacute;n HTML de un mensaje en texto plano.
El formateado ser&aacute; as&iacute;:<br/>
El original es HTML -&gt; texto plano<br/>
<ul>
<li>El texto en <b>negritas</b> aparecer&aacute; enmarcado por <b>*-asteriscos</b>, el texto en <b>cursiva</b> por <b>/-barras</b>.</li>
<li>Los enlaces ser&aacute;n sustitu&iacute;dos por el texto del enlace, seguido por la URL entre par&eacute;ntesis.</li>
<li>Los fragmentos de texto largos quedar&aacute;n divididos en l&iacute;neas de un m&aacute;ximo de 70 caracteres, sin dividir palabras.</li>
</ul>
El original es Texto -&gt; HTML<br/>
<ul>
<li>Dos saltos de l&iacute;nea seguidos ser&aacute;n sustitu&iacute;dos por &lt;p&gt; (p&aacute;rrafo)</li>
<li>Un s&oacute;lo salto de l&iacute;nea ser&aacute; sustitu&iacute;do por &lt;br /&gt; (salto de l&iacute;nea)</li>
<li>Se podr&aacute; pinchar en las direcciones de correo electr&oacute;nico</li>
<li>Se podr&aacute; pinchar en las URL. Estas pueden tener los siguientes formatos:<br/>
<ul><li>http://cierta.p&aacute;gina.url/cierta/direcci&oacute;n/fichero.html
<li>www.urlsitioweb.com
</ul>
Los enlaces creados tendr&aacute;n, con respecto a la hoja de estilo, <i>class</i> &#171;url&#187; y <i>target</i> &#171;_blank&#187;.
</ul>
<b>Advertencia</b>: Si indica que su mensaje es texto plano, pero pega un texto HTML en el formulario, los usuarios que han escogido recibir mensajes en texto plano recibir&aacute;n el mensaje con las etiquetas HTML visibles.
                                                                                                                                                                                                                              help/from.php                                                                                       0000644 0000765 0000765 00000002444 10251663151 014244  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        Puede usar tres m&eacute;todos diferentes para establecer la l&iacute;nea &#171;de&#187;:
<ul>
<li>Una palabra: esto ser&aacute; reformateado y quedar&aacute; as&iacute;: &lt;la palabra&gt;@<?php echo $domain?>
<br>Por ejemplo: <b>informacion</b> se convertir&aacute; en <b>informacion@<?php echo $domain?></b>
<br>En la mayor&iacute;a de los programas lectores de correo el mensaje aparecer&aacute; enviado por <b>informacion@<?php echo $domain?></b>
<li>Dos o m&aacute;s palabras: esto ser&aacute; reformateado y quedar&aacute; as&iacute;: <i>las palabras que escriba</i> &lt;listmaster@<?php echo $domain?>&gt;
<br>Por ejemplo: <b>informaci&oacute;n sobre la lista</b> se convertir&aacute; en <b>informaci&oacute;n sobre la lista &lt;listmaster@<?php echo $domain?>&gt; </b>
<br>En la mayor&iacute;a de los programas lectores de correo el mensaje aparecer&aacute; enviado por <b>informaci&oacute;n sobre la lista</b>
<li>Ninguna o m&aacute;s palabras y una direcci&oacute;n de correo: esto ser&aacute; reformateado y quedar&aacute; as&iacute;: <i>Palabras</i> &lt;direcciondecorreo&gt;
<br>Por ejemplo: <b>Mi Nombre mi@email.com</b> se convertir&aacute; en <b>Mi Nombre &lt;mi@email.com&gt;</b>
<br>En la mayor&iacute;a de los programas lectores de correo el mensaje aparecer&aacute; enviado por <b>Mi Nombre</b>
                                                                                                                                                                                                                            help/mergeattributes.php                                                                            0000644 0000765 0000765 00000001672 10251663151 016511  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        <p>Notas sobre c&oacute;mo fundir atributos.</p>
<p>Fundir atributos significa que los valores correspondientes a los usuarios se mantienen, pero los atributos a los que pertenecen quedan fundidos en uno. El atributo que permanece es el primero (por orden de la lista, tal como se ve en la p&aacute;gina).</p>
<ul>
<li>S&oacute;lo se pueden fundir atributos que sean del mismo tipo.</li>
<li>Al fundir se mantendr&aacute; el valor del primer atributo, si existe. En caso contrario se usar&aacute; el valor del otro atributo que se est&aacute; fundiendo. Esto significa que si ambos atributos tienen ya un valor asignado, se perder&aacute; informaci&oacute;n.</li>
<li>Si funde atributos del tipo <i>casilla de verificaci&oacute;n</i> el atributo fundido resultante ser&aacute; del tipo <i>grupo de casillas de verificaci&oacute;n</i>.</li>
<li>Los atributos que se funden en otro ser&aacute;n eliminados despu&eacute;s de la operaci&oacute;n.</li>
</ul>
                                                                      help/message.php                                                                                    0000644 0000765 0000765 00000003263 10251663151 014725  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        En el campo del mensaje puede utilizar &#171;variables&#187;, que ser&aacute;n sustitu&iacute;das por el valor que convenga a cada usuario:
<br />Las variables deben tener el formato <b>[NOMBRE]</b>, sabiendo que NOMBRE	ser&aacute; sustitu&iacute;do por el nombre de alguno de sus atributos.
<br />Por ejemplo, si tiene un atributo llamado &#171;Nombre de pila&#187;, coloque [NOMBRE DE PILA] en alg&uacute;n lugar del mensaje, donde quiera que aparezca el nombre de pila del destinatario.
</p><p>Actualmente tiene definidos los siguientes atributos:
<table border=1><tr><td><b>Atributo</b></td><td><b>Marcador</b></td></tr>
<?php
$req = Sql_query("select name from {$tables["attribute"]} order by listorder");
while ($row = Sql_Fetch_Row($req))
  if (strlen($row[0]) < 20)
    printf ('<tr><td>%s</td><td>[%s]</td></tr>',$row[0],strtoupper($row[0]));
print '</table>';
if (ENABLE_RSS) {
?>
  <p>Puede crear patrones para los mensajes que se env&iacute;an con elementos RSS. Para ello pinche en la pesta&ntilde;a &#171;Calendario&#187; e indique la frecuencia del mensaje. El mensaje se utilizar&aacute; para enviar la lista de elementos a aquellos usuarios de las listas que hayan escojido esta frecuencia. Debe utilizar el marcador [RSS] en su mensaje para indicar el lugar en el que ir&aacute; la lista de elementos.</p>
<?php }
?>

<p>Para enviar el contenido de una p&aacute;gina web a&ntilde;ada lo que sigue al mensaje:<br/>
<b>[URL:</b>http://www.ejemplo.org/direcci&oacute;n/al/fichero.html<b>]</b></p>
<p>Puede incluir la siguiente informaci&oacute;n del usuario en esta URL: email, foreignkey, id y uniqid.</br>
<b>[URL:</b>http://www.ejemplo.org/perfilusuario.php?email=<b>[</b>email<b>]]</b><br/>
</p>
                                                                                                                                                                                                                                                                                                                                             help/preparemessage.php                                                                             0000644 0000765 0000765 00000003734 10251663151 016307  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        <p>En esta p&aacute;gina puede preparar un mensaje para enviar m&aacute;s tarde. Puede especificar toda la informaci&oacute;n necesaria para el mensaje, excepto la(s) lista(s) a las que enviarlo. Esto lo puede indicar en el momento de enviar el mensaje previamente preparado.</p>
<p>
Su mensaje preparado permanece a&uacute;n despu&eacute;s de enviado, de modo que lo puede reutilizar tantas veces como quiera. Cuidado con esto, porque por descuido puede acabar enviando el mismo mensaje a sus usuarios varias veces.</p>
<p>
Esta funcionalidad est&aacute; pensada especialmente para el caso de que haya m&uacute;ltiples administradores. Si un administrador principal prepara un mensaje, los subadministradores pueden enviarlo a sus propias listas. En este caso puede a&ntilde;adir marcadores adicionales a sus mensajes: los atributos de los administradores.
</p>
<p>Por ejemplo, si tiene un atributo para los administradores llamado <b>Nombre</b> puede a&ntilde;adir [LISTOWNER.NOMBRE] como marcador, y esto ser&aacute; sustitu&iacute;do por el <b>Nombre</b> del due&ntilde;o de la lista a la que se env&iacute;a el mensaje, independientemente de qui&eacute;n lo env&iacute;e. De modo que si el administrador principal env&iacute;a el mensaje a una lista cuyo due&ntilde;o es otra persona los marcadores [LISTOWNER] ser&aacute;n sustitu&iacute;dos por los valores correspondientes al due&ntilde;o de la lista, no al administrador principal.
</P>
<p>N&oacute;tese:
<br/>
El formato de los marcadores [LISTOWNER] es <b>[LISTOWNER.ATRIBUTO]</b><br/>
<p>Actualmente tiene definidos los siguientes atributos de administrador:
<table border=1><tr><td><b>Atributo</b></td><td><b>Marcador</b></td></tr>
<?php
$req = Sql_query("select name from {$tables["adminattribute"]} order by listorder");
if (!Sql_Affected_Rows())
  print '<tr><td colspan=2>None</td></tr>';

while ($row = Sql_Fetch_Row($req))
  if (strlen($row[0]) < 20)
    printf ('<tr><td>%s</td><td>[LISTOWNER.%s]</td></tr>',$row[0],strtoupper($row[0]));

?>
                                    help/repetition.php                                                                                 0000644 0000765 0000765 00000000665 10251663151 015466  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        Esta opci??n le da la posibilidad de hacer que el sistema cree autom??ticamente un mensaje nuevo, copia exacta del mensaje actual, que ser?? reenviado regularmente. Se puede establecer el plazo que separa cada iteraci??n del mensaje.
Si utiliza esta opci??n es importante utilizar tambi??n la opci??n ??Embargo??, porque de otro modo su mensaje se enviar?? continuamente, inundando a sus usuarios con un mont??n de mensajes id??nticos.

                                                                           help/sendformat.php                                                                                 0000644 0000765 0000765 00000001433 10251663151 015440  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        Si este mensaje ha sido formateado como HTML, indique c&oacute;mo quiere enviarlo:<br />
<ul>
<li><b>HTML</b> - HTML a los usuarios que han escogido la opci&oacute;n de recibir correo HTML, y texto plano a todos los dem&aacute;s</li>
<li><b>texto plano</b> - Texto plano a todo el mundo</li>
<li><b>texto plano y HTML</b> - Un correo m&aacute;s voluminoso que contenga tanto la versi&oacute;n HTML como la de texto plano (el correo es mayor pero es m&aacute;s probable que funcione adecuadamente para todos)</li>
<li><b>PDF</b> - El texto del mensaje enviado como adjunto en formato PDF</li>
<li><b>texto plano y PDF</b> - Un correo con el mensaje en texto plano, m&aacute;s un adjunto en formato PDF</li>
</ul>

<b>Nota:</b> La versi&oacute;n PDF se hace a partir del texto plano, no del HTML.
                                                                                                                                                                                                                                     help/subject.php                                                                                    0000644 0000765 0000765 00000000114 10251663151 014730  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        Escriba el asunto de su mensaje. No se pueden usar marcadores en el asunto.
                                                                                                                                                                                                                                                                                                                                                                                                                                                    help/usetemplate.php                                                                                0000644 0000765 0000765 00000000431 10251663151 015623  0                                                                                                    ustar   german                          german                          0000000 0000000                                                                                                                                                                        <p>Si utiliza un patr&oacute;n, este mensaje aparecer&aacute; en el lugar indicado por el marcador [CONTENT] del patr&oacute;n.</p>
<p>Adem&aacute;s de [CONTENT] puede incluir [FOOTER] y [SIGNATURE] para incluir un pie de mensaje y una firma respectivamente. Esto es opcional.</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       