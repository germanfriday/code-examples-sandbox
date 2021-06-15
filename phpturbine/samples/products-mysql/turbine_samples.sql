#
# Create a Database and the Tables needed for the Turbine samples
# A mySQL SQL dump, however should run OK on other SQL databases.
#

CREATE DATABASE IF NOT EXISTS turbine_samples_db;
USE turbine_samples_db;


#
# Create the Products table used on the Products-mysql sample
#

DROP TABLE IF EXISTS Products;

CREATE TABLE Products (
  ProductId int,
  ProductName varchar(255),
  ProductDetails varchar(255),
  ProductImage varchar(255),
  ProductPrice varchar(20)
);


INSERT INTO Products VALUES (1,	"Amaryllis", "Beautiful flowers from the north coast of Abyssinia. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 6 flowers.", "flower.bmp", "15.60");
INSERT INTO Products VALUES (2,	"Bergamot", "A stupendous adorable flowers from the high riffs of the Congo river. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in special luxury pack.", "flower.png", "22.40");
INSERT INTO Products VALUES (3,	"Calendula", "A superb east-asian mesh-flower that grows on the steep mountains of the Ararustra region. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes special box.", "flower.gif", "30.90");
INSERT INTO Products VALUES (4,	"Feverfew", "Magnificent Tahitian bush-bloom with adorable leaves. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Already pre-packaged in sets of 12 units.", "flower.jpg", "18.00");
INSERT INTO Products VALUES (5,	"Freesia", "Delicious-looking from the plains of Monrovia. enean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes a wooden container box.", "flower.tif", "45.90");
INSERT INTO Products VALUES (6,	"Lobelia", "Funny flower, from the high plains of Tornilia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Packaged in translucent plastic.", "flower.gif", "17.10");
INSERT INTO Products VALUES (7,	"Xeranthemum", "Admirable flower, from the middle-mountains of Laurindia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Packaged in plastic box of 12 units.", "flower.tif", "72.81");
INSERT INTO Products VALUES (8,	"Mirabilis", "Beautiful flowers from the north coast of Zurissia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 9 flowers.", "flower.png", "21.90");
INSERT INTO Products VALUES (9,	"Zinnia", "Adorable flowers from the high riffs of the Congo river. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in special luxury pack of 8.", "flower.tif", "87.10");
INSERT INTO Products VALUES (10,	"Portulaca", "The superb west-african mesh-flower that grows on the steep mountains of the Ararustra region. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes special box.", "flower.bmp", "21.00");
INSERT INTO Products VALUES (11,	"Daffodil", "A very nice flower, from the high plains of Tornilia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Packaged in translucent plastic boxlets.", "flower.jpg", "12.50");
INSERT INTO Products VALUES (12,	"Edelweiss", "Beautiful flowers from the high riffs of the Yago river. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 10.", "flower.png", "16.90");
INSERT INTO Products VALUES (13,	"Nemesia", "Superb east-asian mesh-flower that grows on the steep mountains of the Ararustra region. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes special box.", "flower.tif", "25.90");
INSERT INTO Products VALUES (14,	"Hydrangea", "Zundappian bush-flower with adorable leaves. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Pre-packaged in sets of 2 units.", "flower.bmp", "27.90");
INSERT INTO Products VALUES (15,	"Candytuft", "Superb east-european mesh-flower that grows on the steep mountains of the Ararustra region. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes special box.", "flower.jpg", "9.90");
INSERT INTO Products VALUES (16,	"Begonia", "Admirable flower, from the low mountains of Laurindia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Packaged in wooden box of 7 units.", "flower.tif", "9.95");
INSERT INTO Products VALUES (17,	"Asphodeline", "Exquisite flowers from the south coast of Laringia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 2 flowers.", "flower.png", "65.00");
INSERT INTO Products VALUES (18,	"Coreopsis", "The superb east-asian mesh-flower that grows on the steep mountains of the Ararustra region. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes special box.", "flower.bmp", "29.90");
INSERT INTO Products VALUES (19,	"Helenium", "Beautiful flowers from the north coast of Thanassia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 3 flowers.", "flower.gif", "35.90");
INSERT INTO Products VALUES (20,	"Nasturtium", "A nice flower from the plains of Zigzaggia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Includes a metal container box.", "flower.jpg", "89.90");
INSERT INTO Products VALUES (21,	"Matricaria", "Superb all-year flower with adorable leaves. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Pre-packaged in sets of 20 units.", "flower.tif", "34.50");
INSERT INTO Products VALUES (22,	"Waldsteinia", "Beautiful flowers from the north coast of Waldysia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 3 flowers.", "flower.gif", "90.20");
INSERT INTO Products VALUES (23,	"Yarrow", "Very cool flowers from the north coast of Purissia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Available in packs of 9 flowers.", "flower.bmp", "34.20");
INSERT INTO Products VALUES (24,	"Ixia", "Zundappian flower with strong leaves. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Pre-packaged in sets of 20 units.", "flower.png", "18.20");
INSERT INTO Products VALUES (25,	"Godetia", "Admirable flower, from the low mountains of Laurindia. Aenean lorem risus, venenatis eget, ultricies id, imperdiet eget, lacus. Donec gravida vestibulum lectus. Packaged in plastic box of 19 units.", "flower.bmp", "35.00");
