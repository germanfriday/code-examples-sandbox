var Quotation=new Array() 
Quotation[0] = "<em>The greatest motivational act one person can do for another is to listen.</em><br><b>Ron Moody</b>";
Quotation[1] = "<em>You do not merely want to be considered just the best of the best. You want to be considered the only ones who do what you do.</em><br><b>Jerry Garcia</b>";
Quotation[2] = "<em>In the middle of difficulty lies opportunity.</em><br><b>Albert Einstein</b>";
Quotation[3] = "<em>You cannot improve one thing by 1000% but you can improve 1000 little things by 1%</em><br><b>Jan Carlzon</b>";
Quotation[4] = "<em>Customers don't care what you know until they know that you care.</em>";
Quotation[5] = "<em>Coming together is a beginning. Keeping together is progress. Working together is success.</em><br><b>Henry Ford</b>";
Quotation[6] = "<em>Business is not just doing deals; business is having great products, doing great engineering, and providing tremendous service to customers. Finally, business is a cobweb of human relationships.</em><br><b>H. Ross Perot</b>";
Quotation[7] = "<em>Profit in business comes from repeat customers, customers that boast about your project or service, and that bring friends with them.</em><br><b>W. Edwards Deming</b>";
Quotation[8] = "<em>We are what we repeatedly do. Excellence, then, is not an act, but a habit.</em><br><b>Aristotle</b>";
Quotation[9] = "<em>We see our customers as invited guests to a party, and we are the hosts. It's our job every day to make every important aspect of the customer experience a little bit better.</em><br><b>Jeff Bezos</b>";
Quotation[10] = "<em>May the force be with you.</em><br><b>Obi-Wan Kenobi</b>";
Quotation[11] = "<em>Do Great Today!</em><br><b>Chris Schwalbach</b>";
Quotation[12] = "<em>Do what you do so well that they will want to see it again and bring their friends.</em><br><b>Walt Disney</b>";
Quotation[13] = "<em>The customer's perception is your reality.</em><br><b>Kate Zabriskie</b>";
Quotation[14] = "<em>Customers don't expect you to be perfect. They do expect you to fix things when they go wrong.</em><br><b>Donald Porter</b><br><b>";
Quotation[15] = "<em>I tell them there's no problems, only solutions.</em><br><b>John Lennon</b>";
Quotation[16] = "<em>Customer service is really the pulse of a company.  It's where you learn about what's working and what's not.   It sets the standard for conduct throughout the company.</em><br><b>Lisa McKenzie</b>";
Quotation[17] = "<em>Create a cause, not just a product.  A cause is a way of doing things that catalyzes loyalty... transforming customer into unpaid salespeople.</em><br><b>Guy Kawasaki</b>";

var Q = Quotation.length;
var whichQuotation=Math.round(Math.random()*(Q-1));
function showQuotation(){document.write(Quotation[whichQuotation]);}

