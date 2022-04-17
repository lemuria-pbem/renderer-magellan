VERSION 69
RULES "$Id: lemuria.cr 69"

MAGELLAN
"magellan.library.gamebinding.EresseaSpecificStuff";class
"LEMURIA";orderFileStartingString

ORDER "COMMENT"
"// Text1*";syntax
"KOMMENTAR Text1*";syntax
"//";locale_de
"KOMMENTAR";locale_de

ORDER "PCOMMENT"
";";locale_de
1;internal

ORDER "ATTACK"
"ATTACKIERE u1";syntax
"ATTACKIERE";locale_de
"ATTACKIEREN";locale_de

ORDER "BANNER"
"BANNER Text";syntax
"BANNER";locale_de

ORDER "STEAL"
"BEKLAUE u1";syntax
"BEKLAUE";locale_de
"BEKLAUEN";locale_de

ORDER "SIEGE"
"BELAGERE b1";syntax
"BELAGERE";locale_de

ORDER "NAME"
"BENENNE EINHEIT | PARTEI | GEBÄUDE | BURG | SCHIFF | REGION | (FREMDE EINHEIT u1) | (FREMDES SCHIFF s1) | (FREMDES GEBÄUDE b1) | (FREMDE BURG b1) | (FREMDE PARTEI f1) Text";syntax
"BENENNE";locale_de
"BENENNEN";locale_de

ORDER "USE"
"BENUTZE [1] Ding";syntax
"BENUTZE";locale_de
"BENUTZEN";locale_de

ORDER "DESCRIBE"
"BESCHREIBE REGION | SCHIFF | GEBÄUDE | BURG | EINHEIT | PRIVAT  Text";syntax
"BESCHREIBE";locale_de
"BESCHREIBEN";locale_de
"TEXT";locale_de

ORDER "ENTER"
"BETRETE (b1 | s1 | BURG b1 | SCHIFF s1 | Ding x1)";syntax
"BETRETE";locale_de
"BETRETEN";locale_de
"BESTEIGE";locale_de
"BESTEIGEN";locale_de

ORDER "GUARD"
"BEWACHE [NICHT]";syntax
"BEWACHE";locale_de
"BEWACHEN";locale_de

ORDER "MESSAGE"
"BOTSCHAFT REGION | (SCHIFF s1) | (GEBÄUDE b2) | (BURG b3) | (EINHEIT u4) | (PARTEI f5)  Text";syntax
"BOTSCHAFT";locale_de

ORDER "DEFAULT"
"DEFAULT Order";syntax
"DEFAULT";locale_de
"VORLAGE";locale_de

ORDER "UNIT"
"EINHEIT u1";syntax
"EINHEIT";locale_de
1;internal

ORDER "END"
"ENDE";syntax
"ENDE";locale_de

ORDER "FOLLOW"
"FOLGE (EINHEIT u1) | (SCHIFF s2)";syntax
"FOLGE";locale_de
"FOLGEN";locale_de

ORDER "RESEARCH"
"FORSCHE KRÄUTER";syntax
"FORSCHE";locale_de
"FORSCHEN";locale_de
"ERFORSCHEN";locale_de

ORDER "GIVE"
"GIB (u1|0) ( 1 | (JE 2) | ALLES  Ding | PERSONEN ) | ALLES | KRÄUTER | KOMMANDO | EINHEIT";syntax
"GIB";locale_de
"GEBEN";locale_de

ORDER "HELP"
"HELFE f1 ALLES|GIB|KÄMPFE|BEWACHE|SILBER|PARTEITARNUNG [NICHT] ";syntax
"HELFE";locale_de
"HELFEN";locale_de

ORDER "COMBAT"
"KÄMPFE [AGGRESSIV|HINTEN|DEFENSIV|NICHT|FLIEHE|(HELFE [NICHT])]";syntax
"KÄMPFE";locale_de
"KÄMPFEN";locale_de

ORDER "COMBATSPELL"
"KAMPFZAUBER [STUFE 1] Zauber [NICHT]";syntax
"KAMPFZAUBER";locale_de

ORDER "BUY"
"KAUFE 1 Luxus";syntax
"KAUFE";locale_de
"KAUFEN";locale_de

ORDER "CONTACT"
"KONTAKTIERE (EINHEIT)|(FACTION) u1";syntax
"KONTAKTIERE";locale_de
"KONTAKTIEREN";locale_de

ORDER "TEACH"
"LEHRE u1+";syntax
"LEHRE";locale_de
"LEHREN";locale_de

ORDER "LEARN"
"LERNE (Talent [1])|(AUTO Talent)";syntax
"LERNE";locale_de
"LERNEN";locale_de

ORDER "LOCALE"
"LOCALE Sprache";syntax
"LOCALE";locale_de
1;internal

ORDER "MAKE"
"MACHE [(TEMP u1 [Name]) | ([1] Ding [s1|b1]) | ([1] STRAßE Richtung)]";syntax
"MACHE";locale_de
"MACHEN";locale_de

ORDER "MOVE"
"NACH Richtung1+";syntax
"NACH";locale_de
"REISE";locale_de
"REISEN";locale_de

ORDER "NEXT"
"Invalid";syntax
"NÄCHSTER";locale_de
1;internal

ORDER "NUMBER"
"NUMMER (EINHEIT) | (SCHIFF) | (GEBÄUDE) | (BURG) | (PARTEI) [x1]";syntax
"NUMMER";locale_de

ORDER "FACTION"
"PARTEI f1";syntax
"PARTEI";locale_de
1;internal

ORDER "REGION"
"REGION 1 , 2";syntax
"REGION";locale_de
1;internal

ORDER "RECRUIT"
"REKRUTIERE 1";syntax
"REKRUTIERE";locale_de
"REKRUTIEREN";locale_de

ORDER "RESERVE"
"RESERVIERE (1) | (ALLES) | (JE 2) Gegenstand";syntax
"RESERVIERE";locale_de
"RESERVIEREN";locale_de

ORDER "ROUTE"
"ROUTE Richtung1+";syntax
"ROUTE";locale_de

ORDER "SORT"
"SORTIERE (VOR) | (HINTER) u1";syntax
"SORTIERE";locale_de
"SORTIEREN";locale_de

ORDER "SPY"
"SPIONIERE u1";syntax
"SPIONIERE";locale_de
"SPIONIEREN";locale_de

ORDER "HIDE"
"TARNE ([1]) | (Rasse) | (PARTEI [NICHT]) | (PARTEI NUMMER f1)";syntax
"TARNE";locale_de
"TARNEN";locale_de

ORDER "TAX"
"TREIBE [1]";syntax
"TREIBE";locale_de
"TREIBEN";locale_de

ORDER "ENTERTAIN"
"UNTERHALTE [1]";syntax
"UNTERHALTE";locale_de
"UNTERHALTEN";locale_de

ORDER "ORIGIN"
"URSPRUNG 1 2";syntax
"URSPRUNG";locale_de

ORDER "SELL"
"VERKAUFE 1 | ALLES  Luxus";syntax
"VERKAUFE";locale_de
"VERKAUFEN";locale_de

ORDER "LEAVE"
"VERLASSE";syntax
"VERLASSE";locale_de
"VERLASSEN";locale_de

ORDER "CAST"
"ZAUBERE [REGION 1 2] [STUFE 3] Zauber Parameter1*";syntax
"ZAUBERE";locale_de

ORDER "DESTROY"
"ZERSTÖRE [1] [STRASSE richtung]";syntax
"ZERSTÖRE";locale_de
"ZERSTÖREN";locale_de

ORDER "GROW"
"(ZÜCHTE PFERDE)|(ZÜCHTE [1] KRÄUTER)";syntax
"ZÜCHTE";locale_de
"ZÜCHTEN";locale_de

ORDER "PERSISTENT"
"";locale_de
1;internal

ORDER "AFTER"
"HINTER";locale_de
1;internal

ORDER "ALL"
"ALLES";locale_de
1;internal

ORDER "AURA"
"AURA";locale_de
1;internal

ORDER "BEFORE"
"VOR";locale_de
1;internal

ORDER "CASTLE"
"BURG";locale_de
1;internal

ORDER "BUILDING"
"GEBÄUDE";locale_de
1;internal

ORDER "COMBAT_"
"KÄMPFE";locale_de
1;internal

ORDER "COMBAT_AGGRESSIVE"
"AGGRESSIV";locale_de
1;internal

ORDER "COMBAT_DEFENSIVE"
"DEFENSIV";locale_de
1;internal

ORDER "COMBAT_FLEE"
"FLIEHE";locale_de
1;internal

ORDER "COMBAT_FRONT"
"VORNE";locale_de
1;internal

ORDER "COMBAT_REAR"
"HINTEN";locale_de
1;internal

ORDER "COMBAT_NOT"
"NICHT";locale_de
1;internal

ORDER "CONTROL"
"KOMMANDO";locale_de
1;internal

ORDER "LEMURIA"
"LEMURIA";locale_de
1;internal

ORDER "PARAMETER_FACTION"
"PARTEI";locale_de
1;internal

ORDER "HELP_ALL"
"ALLES";locale_de
1;internal

ORDER "HELP_COMBAT"
"KÄMPFE";locale_de
1;internal

ORDER "HELP_FACTIONSTEALTH"
"PARTEITARNUNG";locale_de
1;internal

ORDER "HELP_GIVE"
"GIB";locale_de
1;internal

ORDER "HELP_GUARD"
"BEWACHE";locale_de
"BEWACHEN";locale_de
1;internal

ORDER "HELP_SILVER"
"SILBER";locale_de
1;internal

ORDER "COMBAT_HELP"
"HELFE";locale_de
1;internal

ORDER "HERBS"
"KRÄUTER";locale_de
1;internal

ORDER "HORSES"
"PFERDE";locale_de
1;internal

ORDER "LEVEL"
"STUFE";locale_de
1;internal

ORDER "MEN"
"PERSONEN";locale_de
1;internal

ORDER "NOT"
"NICHT";locale_de
1;internal

ORDER "STEALTH_NUMBER"
"NUMMER";locale_de
1;internal

ORDER "PAUSE"
"PAUSE";locale_de
1;internal

ORDER "ROAD"
"STRAßE";locale_de
1;internal

ORDER "SHIP"
"SCHIFF";locale_de
1;internal

ORDER "TEMP"
"TEMP";locale_de
1;internal

ORDER "UNIT"
"EINHEIT";locale_de
1;internal

ORDER "NORTHWEST"
"NORDWESTEN";locale_de
"NW";locale_de
1;internal

ORDER "NORTHEAST"
"NORDOSTEN";locale_de
1;internal

ORDER "EAST"
"OSTEN";locale_de
1;internal

ORDER "SOUTHEAST"
"SÜDOSTEN";locale_de
"SO";locale_de
1;internal

ORDER "SOUTHWEST"
"SÜDWESTEN";locale_de
"SW";locale_de
1;internal

ORDER "WEST"
"WESTEN";locale_de
1;internal

ORDER "NW"
"NW";locale_de
"NORDWESTEN";locale_de
1;internal

ORDER "NE"
"NO";locale_de
"NORDOSTEN";locale_de
1;internal

ORDER "E"
"O";locale_de
"OSTEN";locale_de
1;internal

ORDER "SE"
"SO";locale_de
"SÜDOSTEN";locale_de
1;internal

ORDER "SW"
"SW";locale_de
"SÜDWESTEN";locale_de
1;internal

ORDER "W"
"W";locale_de
"WESTEN";locale_de
1;internal

ALLIANCECATEGORY "ALLES"
"ALL";name
59;bitmask

ALLIANCECATEGORY "SILBER"
"SILVER";name
"ALLES";parent
1;bitmask

ALLIANCECATEGORY "KÄMPFE"
"COMBAT";name
"ALLES";parent
2;bitmask

ALLIANCECATEGORY "GIB"
"GIVE";name
"ALLES";parent
8;bitmask

ALLIANCECATEGORY "BEWACHEN"
"GUARD";name
"ALLES";parent
16;bitmask

ALLIANCECATEGORY "PARTEITARNUNG"
"FACTIONSTEALTH";name
"ALLES";parent
32;bitmask

ITEMCATEGORY "silver"
"Silber";name
0;naturalorder

ITEMCATEGORY "weapons"
"Waffen";name
1;naturalorder

ITEMCATEGORY "front weapons"
"Front-Waffen";name
0;naturalorder
"weapons";parent

ITEMCATEGORY "distance weapons"
"Distanz-Waffen";name
1;naturalorder
"weapons";parent

ITEMCATEGORY "ammunition"
"Munition";name
2;naturalorder
"weapons";parent

ITEMCATEGORY "armour"
"Rüstungen";name
2;naturalorder

ITEMCATEGORY "shield"
"Schilde";name
0;naturalorder
"armour";parent

ITEMCATEGORY "resources"
"Ressourcen";name
3;naturalorder

ITEMCATEGORY "luxuries"
"Luxusgüter";name
4;naturalorder

ITEMCATEGORY "herbs"
"Kräuter";name
"kraeuter";iconname
5;naturalorder

ITEMCATEGORY "potions"
"Tränke";name
6;naturalorder

ITEMCATEGORY "trophies"
"Trophäen";name
7;naturalorder

ITEMCATEGORY "misc"
"Sonstiges";name
8;naturalorder

SKILLCATEGORY "war"
"Kampf";name
0;naturalorder

SKILLCATEGORY "magic"
"Magie";name
1;naturalorder

SKILLCATEGORY "resource"
"Resourcen-Gewinnung";name
2;naturalorder

SKILLCATEGORY "silver"
"Silber-Gewinnung";name
0;naturalorder
"resource";parent

SKILLCATEGORY "build"
"Bauen";name
3;naturalorder

SKILLCATEGORY "movement"
"Bewegung";name
4;naturalorder

SKILLCATEGORY "trade"
"Handel";name
5;naturalorder

SKILLCATEGORY "misc"
"Sonstiges";name
6;naturalorder

HERB "Flachwurz"
"Flachwurz";name
"Ebene";region
"herbs";category
"flatroot";iconname

HERB "Würziger Wagemut"
"Würziger Wagemut";name
"Ebene";region
"herbs";category
"tangy temerity";iconname

HERB "Eulenauge"
"Eulenauge";name
"Ebene";region
"herbs";category
"owlsgaze";iconname

HERB "Grüner Spinnerich"
"Grüner Spinnerich";name
"Wald";region
"herbs";category
"spider ivy";iconname

HERB "Blauer Baumringel"
"Blauer Baumringel";name
"Wald";region
"herbs";category
"cobalt fungus";iconname

HERB "Elfenlieb"
"Elfenlieb";name
"Wald";region
"herbs";category
"elvendear";iconname

HERB "Gurgelkraut"
"Gurgelkraut";name
"Sumpf";region
"herbs";category
"bugleweed";iconname

HERB "Knotiger Saugwurz"
"Knotiger Saugwurz";name
"Sumpf";region
"herbs";category
"knotroot";iconname

HERB "Blasenmorchel"
"Blasenmorchel";name
"Sumpf";region
"herbs";category
"bubblemorel";iconname

HERB "Wasserfinder"
"Wasserfinder";name
"Wüste";region
"herbs";category
"waterfinder";iconname

HERB "Kakteenschwitz"
"Kakteenschwitz";name
"Wüste";region
"herbs";category
"peyote";iconname

HERB "Sandfäule"
"Sandfäule";name
"Wüste";region
"herbs";category
"sandreeker";iconname

HERB "Windbeutel"
"Windbeutel";name
"Hochland";region
"herbs";category
"windbag";iconname

HERB "Fjordwuchs"
"Fjordwuchs";name
"Hochland";region
"herbs";category
"fjord fungus";iconname

HERB "Alraune"
"Alraune";name
"Hochland";region
"herbs";category
"mandrake";iconname

HERB "Steinbeißer"
"Steinbeißer";name
"Berge";region
"herbs";category
"rockweed";iconname

HERB "Spaltwachs"
"Spaltwachs";name
"Berge";region
"herbs";category
"gapgrowth";iconname

HERB "Höhlenglimm"
"Höhlenglimm";name
"Berge";region
"herbs";category
"cavelichen";iconname

HERB "Eisblume"
"Eisblume";name
"Gletscher";region
"herbs";category
"ice begonia";iconname

HERB "Weißer Wüterich"
"Weißer Wüterich";name
"Gletscher";region
"herbs";category
"white hemlock";iconname

HERB "Schneekristall"
"Schneekristall";name
"Gletscher";region
"herbs";category
"snowcrystal petal";iconname

SKILL "Alchemie"
"Alchemie";name
"build";category
250;cost
SKILL "Armbrustschießen"
"Armbrustschießen";name
"war";category
SKILL "Ausdauer"
"Ausdauer";name
"misc";category
SKILL "Bergbau"
"Bergbau";name
"resource";category
SKILL "Bogenschießen"
"Bogenschießen";name
"war";category
SKILL "Burgenbau"
"Burgenbau";name
"build";category
SKILL "Handeln"
"Handeln";name
"trade";category
SKILL "Hiebwaffen"
"Hiebwaffen";name
"war";category
SKILL "Holzfällen"
"Holzfällen";name
"resource";category
SKILL "Katapultbedienung"
"Katapultbedienung";name
"war";category
SKILL "Kräuterkunde"
"Kräuterkunde";name
"resource";category
100;cost
SKILL "Magie"
"Magie";name
"magic";category
COSTS
100;1
250;2
400;3
550;4
700;5
850;6
1000;7
1150;8
1300;9
1450;10
1600;11
1750;12
1900;13
2050;14
2200;15
2350;16
2500;17
2650;18
2800;19
2950;20
3100;21
3250;22
3400;23
3550;24
3700;25
3850;26
4000;27
4150;28
4300;29
4450;30
4600;31
4750;32
4900;33
5050;34
5200;35
5350;36
5500;37
5650;38
5800;39
5950;40
6100;41
6250;42
6400;43
6550;44
6700;45
6850;46
7000;47
7150;48
7300;49
7450;50
SKILL "Pferdedressur"
"Pferdedressur";name
"resource";category
SKILL "Reiten"
"Reiten";name
"movement";category
SKILL "Rüstungsbau"
"Rüstungsbau";name
"build";category
SKILL "Schiffbau"
"Schiffbau";name
"build";category
SKILL "Segeln"
"Segeln";name
"movement";category
SKILL "Spionage"
"Spionage";name
"misc";category
150;cost
SKILL "Stangenwaffen"
"Stangenwaffen";name
"war";category
SKILL "Steinbau"
"Steinbau";name
"resource";category
SKILL "Steuereintreiben"
"Steuereintreiben";name
"silver";category
SKILL "Straßenbau"
"Straßenbau";name
"build";category
SKILL "Taktik"
"Taktik";name
"war";category
100;cost
SKILL "Tarnung"
"Tarnung";name
"misc";category
SKILL "Unterhaltung"
"Unterhaltung";name
"silver";category
SKILL "Waffenbau"
"Waffenbau";name
"build";category
SKILL "Wagenbau"
"Wagenbau";name
"build";category
SKILL "Wahrnehmung"
"Wahrnehmung";name
"misc";category

RACE "Zwerge"
"Zwerge";name
110;recruitmentcosts
10;weight
5.0;capacity
TALENTBONI
2;Bergbau
-1;Bogenschießen
2;Burgenbau
1;Handeln
1;Hiebwaffen
-1;Holzfällen
2;Katapultbedienung
-2;Kräuterkunde
-2;Magie
-2;Pferdedressur
-2;Reiten
2;Rüstungsbau
-1;Schiffbau
-2;Segeln
2;Steinbau
1;Steuereintreiben
2;Straßenbau
-1;Tarnung
-1;Unterhaltung
2;Waffenbau
TALENTBONI "Berge"
1;Taktik
TALENTBONI "Gletscher"
1;Taktik

RACE "Orks"
"Orks";name
70;recruitmentcosts
10;weight
5.0;capacity
2;recruitmentfactor
TALENTBONI
1;Alchemie
1;Bergbau
1;Burgenbau
-3;Handeln
1;Holzfällen
-2;Kräuterkunde
-1;Magie
-1;Pferdedressur
1;Rüstungsbau
-1;Schiffbau
-1;Segeln
-1;Spionage
1;Steinbau
1;Steuereintreiben
1;Taktik
-2;Unterhaltung
2;Waffenbau
-1;Wagenbau

RACE "Elfen"
"Elfen";name
130;recruitmentcosts
10;weight
5.0;capacity
TALENTBONI
-1;Alchemie
-2;Bergbau
2;Bogenschießen
-1;Burgenbau
-2;Katapultbedienung
2;Kräuterkunde
1;Magie
1;Pferdedressur
-1;Rüstungsbau
-1;Schiffbau
-1;Segeln
-1;Steinbau
-1;Straßenbau
1;Tarnung
1;Wahrnehmung
TALENTBONI "Wald"
1;Tarnung
1;Wahrnehmung
2;Taktik

RACE "Halblinge"
"Halblinge";name
60;recruitmentcosts
8;weight
5.0;capacity
TALENTBONI
1;Armbrustschießen
1;Bergbau
-1;Bogenschießen
1;Burgenbau
2;Handeln
-1;Hiebwaffen
-1;Katapultbedienung
2;Kräuterkunde
-1;Pferdedressur
-1;Reiten
-1;Schiffbau
-2;Segeln
1;Spionage
-1;Stangenwaffen
-1;Steuereintreiben
1;Straßenbau
1;Tarnung
1;Unterhaltung
2;Wagenbau
1;Wahrnehmung

RACE "Menschen"
"Menschen";name
75;recruitmentcosts
10;weight
5.0;capacity
TALENTBONI
1;Handeln
-1;Kräuterkunde
1;Schiffbau
1;Segeln

RACE "Trolle"
"Trolle";name
90;recruitmentcosts
20;weight
10.0;capacity
TALENTBONI
2;Bergbau
-2;Bogenschießen
2;Burgenbau
1;Hiebwaffen
2;Katapultbedienung
-1;Kräuterkunde
-1;Pferdedressur
-2;Reiten
2;Rüstungsbau
-1;Schiffbau
-1;Segeln
-3;Spionage
2;Steinbau
1;Steuereintreiben
2;Straßenbau
-1;Taktik
-3;Tarnung
-1;Unterhaltung
-1;Wahrnehmung

RACE "Aquaner"
"Aquaner";name
80;recruitmentcosts
10;weight
5.0;capacity
TALENTBONI
-2;Bergbau
-1;Burgenbau
2;Handeln
-1;Rüstungsbau
3;Schiffbau
3;Segeln
-1;Straßenbau
SPECIALS
1;shiprange

RACE "Bauern"
"Bauern";name
10;weight

RACE "Ents"
"Ents";name
50;weight

RACE "Zombies"
"Zombies";name
0;maintenance
10;weight

RACE "Skelette"
"Skelette";name
0;maintenance
5;weight

RACE "Ghoule"
"Ghoule";name
0;maintenance
10;weight

ITEM "Silber"
"Silber";name
0.01;weight
"silver";category
1;storeinbonw

ITEM "Juwel"
"Juwel";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
7;Silber

ITEM "Weihrauch"
"Weihrauch";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
4;Silber

ITEM "Balsam"
"Balsam";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
4;Silber

ITEM "Gewürz"
"Gewürz";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
5;Silber

ITEM "Myrrhe"
"Myrrhe";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
5;Silber

ITEM "Öl"
"Öl";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
3;Silber

ITEM "Seide"
"Seide";name
1;weight
"luxuries";category
1;storeinbonw
RESOURCES
6;Silber

ITEM "Eisen"
"Eisen";name
5;weight
"Bergbau";makeskill
"resources";category
1;storeinbonw

ITEM "Holz"
"Holz";name
5;weight
"Holzfällen";makeskill
"resources";category
1;storeinbonw

ITEM "Stein"
"Stein";name
60;weight
"Steinbau";makeskill
"resources";category
0;storeinbonw

ITEM "Steine"
"Steine";name
60;weight
"Steinbau";makeskill
"resources";category
0;storeinbonw

ITEM "Pferd"
"Pferd";name
50;weight
"Pferdedressur";makeskill
1;makeskilllevel
"resources";category
0;storeinbonw
1;ishorse

ITEM "Pferde"
"Pferde";name
50;weight
"Pferdedressur";makeskill
1;makeskilllevel
"resources";category
0;storeinbonw
1;ishorse

ITEM "Kräuter"
"Kräuter";name
0.01;weight
"Kräuterkunde";makeskill
1;makeskilllevel
"resources";category

ITEM "Wagen"
"Wagen";name
40;weight
"Wagenbau";makeskill
1;makeskilllevel
"resources";category
0;storeinbonw
RESOURCES
5;Holz

ITEM "Katapult"
"Katapult";name
100;weight
"Wagenbau";makeskill
"Katapultbedienung";useskill
5;makeskilllevel
"distance weapons";category
0;storeinbonw
RESOURCES
10;Holz

ITEM "Schwert"
"Schwert";name
1;weight
"Waffenbau";makeskill
"Hiebwaffen";useskill
2;makeskilllevel
"weapons";category
1;storeinbonw
RESOURCES
1;Eisen

ITEM "Schartiges Schwert"
"Schartiges Schwert";name
1;weight
"Hiebwaffen";useskill
"weapons";category
1;storeinbonw

ITEM "Speer"
"Speer";name
1;weight
"Waffenbau";makeskill
"Stangenwaffen";useskill
1;makeskilllevel
"weapons";category
1;storeinbonw
RESOURCES
1;Holz

ITEM "Rostige Hellebarde"
"Rostige Hellebarde";name
2;weight
"Stangenwaffen";useskill
"weapons";category
1;storeinbonw

ITEM "Streitaxt"
"Streitaxt";name
2;weight
"Waffenbau";makeskill
"Hiebwaffen";useskill
5;makeskilllevel
"weapons";category
1;storeinbonw
RESOURCES
3;Eisen
2;Holz

ITEM "Rostige Kriegsaxt"
"Rostige Kriegsaxt";name
2;weight
"Hiebwaffen";useskill
"weapons";category
1;storeinbonw

ITEM "Armbrust"
"Armbrust";name
2;weight
"Waffenbau";makeskill
"Armbrustschießen";useskill
3;makeskilllevel
"distance weapons";category
1;storeinbonw
RESOURCES
1;Holz

ITEM "Bogen"
"Bogen";name
1;weight
"Waffenbau";makeskill
"Bogenschießen";useskill
2;makeskilllevel
"distance weapons";category
1;storeinbonw
RESOURCES
1;Holz

ITEM "Kettenhemd"
"Kettenhemd";name
2;weight
"Rüstungsbau";makeskill
3;makeskilllevel
"armour";category
1;storeinbonw
RESOURCES
3;Eisen

ITEM "Rostiges Kettenhemd"
"Rostiges Kettenhemd";name
"armour";category
2;weight
1;storeinbonw

ITEM "Plattenpanzer"
"Plattenpanzer";name
4;weight
"Rüstungsbau";makeskill
4;makeskilllevel
"armour";category
1;storeinbonw
RESOURCES
5;Eisen

ITEM "Rostiger Plattenpanzer"
"Rostiger Plattenpanzer";name
4;weight
"armour";category
1;storeinbonw

ITEM "Holzschild"
"Holzchild";name
1;weight
"Rüstungsbau";makeskill
2;makeskilllevel
"shield";category
1;storeinbonw
RESOURCES
1;Holz

ITEM "Eisenschild"
"Eisenschild";name
1;weight
"Rüstungsbau";makeskill
2;makeskilllevel
"shield";category
1;storeinbonw
RESOURCES
1;Eisen

ITEM "Rostiges Schild"
"Rostiges Schild";name
"shield";category
1;weight
1;storeinbonw

ITEM "Rostiger Schild"
"Rostiger Schild";name
"shield";category
1;weight
1;storeinbonw

ITEM "Siebenmeilentee"
"Siebenmeilentee";name
"Alchemie";makeskill
"potions";category
2;makeskilllevel
RESOURCES
1;Blauer Baumringel
1;Windbeutel

ITEM "Goliathwasser"
"Goliathwasser";name
"Alchemie";makeskill
"potions";category
2;makeskilllevel
RESOURCES
1;Gurgelkraut
1;Fjordwuchs


ITEM "Wasser des Lebens"
"Wasser des Lebens";name
"Alchemie";makeskill
"potions";category
2;makeskilllevel
RESOURCES
1;Elfenlieb
1;Knotiger Saugwurz


ITEM "Trank der Wahrheit"
"Trank der Wahrheit";name
"Alchemie";makeskill
"potions";category
2;makeskilllevel
RESOURCES
1;Flachwurz
1;Fjordwuchs


ITEM "Schaffenstrunk"
"Schaffenstrunk";name
"Alchemie";makeskill
4;makeskilllevel
"potions";category
RESOURCES
1;Alraune
1;Spaltwachs
1;Würziger Wagemut


ITEM "Wundsalbe"
"Wundsalbe";name
"Alchemie";makeskill
4;makeskilllevel
"potions";category
RESOURCES
1;Weißer Wüterich
1;Blauer Baumringel
1;Würziger Wagemut

ITEM "Gehirnschmalz"
"Gehirnschmalz";name
"Alchemie";makeskill
6;makeskilllevel
"potions";category
RESOURCES
1;Wasserfinder
1;Steinbeißer
1;Windbeutel
1;Gurgelkraut

ITEM "Pferdeglück"
"Pferdeglück";name
"Alchemie";makeskill
6;makeskilllevel
"potions";category
RESOURCES
1;Blauer Baumringel
1;Sandfäule
1;Kakteenschwitz
1;Knotiger Saugwurz

ITEM "Berserkerblut"
"Berserkerblut";name
"Alchemie";makeskill
6;makeskilllevel
"potions";category
RESOURCES
1;Weißer Wüterich
1;Alraune
1;Flachwurz
1;Sandfäule

ITEM "Bauernlieb"
"Bauernlieb";name
"Alchemie";makeskill
8;makeskilllevel
"potions";category
RESOURCES
1;Alraune
1;Schneekristall
1;Steinbeißer
1;Blasenmorchel
1;Elfenlieb

ITEM "Elixier der Macht"
"Elixier der Macht";name
"Alchemie";makeskill
8;makeskilllevel
"potions";category
RESOURCES
1;Elfenlieb
1;Wasserfinder
1;Windbeutel
1;Grüner Spinnerich
1;Blasenmorchel
1;Drachenblut

ITEM "Heiltrank"
"Heiltrank";name
"Alchemie";makeskill
8;makeskilllevel
"potions";category
RESOURCES
1;Gurgelkraut
1;Windbeutel
1;Eisblume
1;Elfenlieb
1;Spaltwachs

ITEM "Goblinkopf"
"Goblinkopf";name
"trophies";category
0.01;weight
1;storeinbonw

ITEM "Zauberbeutel"
"Zauberbeutel";name
"misc";category
1;weight
0;storeinbonw

ITEM "Seeschlangenkopf"
"Seeschlangenkopf";name
5;weight
"misc";category
1;storeinbonw

ITEM "Ring der Macht"
"Ring der Macht";name
0;weight
"misc";category
1;storeinbonw

ITEM "Drachenblut"
"Drachenblut";name
"misc";category
1;weight
1;storeinbonw

SHIPTYPE "Boot"
"Boot";name
5;size
1;level
2;range
50;capacity
1;captainlevel
2;sailorlevel

SHIPTYPE "Langboot"
"Langboot";name
50;size
1;level
3;range
500;capacity
1;captainlevel
10;sailorlevel

SHIPTYPE "Drachenschiff"
"Drachenschiff";name
100;size
2;level
5;range
"$range + max(0, log_3(($currentcaptainlevel / $captainlevel)))";rangeformula
1000;capacity
2;captainlevel
50;sailorlevel

SHIPTYPE "Karavelle"
"Karavelle";name
250;size
3;level
5;range
3000;capacity
3;captainlevel
30;sailorlevel

SHIPTYPE "Trireme"
"Trireme";name
200;size
4;level
7;range
2000;capacity
4;captainlevel
120;sailorlevel

SHIPTYPE "Galeone"
"Galeone";name
2000;size
5;level
5;range
20000;capacity
5;captainlevel
250;sailorlevel
2;minSailorlevel

CASTLETYPE "Grundmauern"
"Grundmauern";name
1;level
1;minsize
1;maxsize
11;wage
0;tradetax
RAWMATERIALS
1;Stein

CASTLETYPE "Handelsposten"
"Handelsposten";name
1;level
2;minsize
9;maxsize
11;wage
0;tradetax
RAWMATERIALS
1;Stein

CASTLETYPE "Befestigung"
"Befestigung";name
2;level
10;minsize
49;maxsize
12;wage
6;tradetax
RAWMATERIALS
1;Stein

CASTLETYPE "Turm"
"Turm";name
3;level
50;minsize
249;maxsize
13;wage
12;tradetax
RAWMATERIALS
1;Stein

CASTLETYPE "Burg"
"Burg";name
4;level
250;minsize
1249;maxsize
14;wage
18;tradetax
RAWMATERIALS
1;Stein

CASTLETYPE "Festung"
"Festung";name
5;level
1250;minsize
6249;maxsize
15;wage
24;tradetax
RAWMATERIALS
1;Stein

CASTLETYPE "Zitadelle"
"Zitadelle";name
6;level
6250;minsize
16;wage
30;tradetax
RAWMATERIALS
1;Stein

BUILDINGTYPE "Leuchtturm"
"Leuchtturm";name
3;level
MAINTENANCE
100;Silber
RAWMATERIALS
2;Stein
1;Holz
1;Eisen
100;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Bergwerk"
"Bergwerk";name
4;level
MAINTENANCE
500;Silber
RAWMATERIALS
5;Stein
10;Holz
1;Eisen
250;Silber
TALENTBONI
1;Bergbau
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Steinbruch"
"Steinbruch";name
2;level
MAINTENANCE
250;Silber
RAWMATERIALS
1;Stein
5;Holz
1;Eisen
250;Silber
TALENTBONI
1;Steinbau
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Sägewerk"
"Sägewerk";name
3;level
MAINTENANCE
250;Silber
RAWMATERIALS
5;Stein
5;Holz
3;Eisen
200;Silber
TALENTBONI
1;Holzfällen
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Schmiede"
"Schmiede";name
3;level
MAINTENANCE
300;Silber
MAINTENANCE
1;Holz
TALENTBONI
1;Rüstungsbau
1;Waffenbau
RAWMATERIALS
5;Stein
5;Holz
2;Eisen
200;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Pferdezucht"
"Pferdezucht";name
2;level
MAINTENANCE
150;Silber
RAWMATERIALS
2;Stein
4;Holz
1;Eisen
100;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Hafen"
"Hafen";name
3;level
25;maxsize
MAINTENANCE
250;Silber
RAWMATERIALS
5;Stein
5;Holz
250;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Taverne"
"Taverne";name
2;level
MAINTENANCE
5;Silber pro Größenpunkt
RAWMATERIALS
1;Eisen
4;Stein
3;Holz
200;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Akademie"
"Akademie";name
3;level
25;maxsize
MAINTENANCE
1000;Silber
RAWMATERIALS
5;Stein
5;Holz
1;Eisen
500;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Magierturm"
"Magierturm";name
5;level
50;maxsize
MAINTENANCE
1000;Silber
RAWMATERIALS
5;Stein
3;Holz
2;Mallorn
3;Eisen
2;Laen
500;Silber
TALENTBONI
1;Magie
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Karawanserei"
"Karawanserei";name
2;level
10;maxsize
MAINTENANCE
3000;Silber
2;Pferd
RAWMATERIALS
1;Stein
5;Holz
1;Eisen
500;Silber
REGIONTYPES
"Wüste"

BUILDINGTYPE "Damm"
"Damm";name
4;level
50;maxsize
MAINTENANCE
1000;Silber
3;Holz
RAWMATERIALS
5;Stein
10;Holz
1;Eisen
500;Silber
REGIONTYPES
"Sumpf"

BUILDINGTYPE "Tunnel"
"Tunnel";name
6;level
100;maxsize
MAINTENANCE
100;Silber
2;Stein
RAWMATERIALS
10;Stein
5;Holz
1;Eisen
300;Silber
REGIONTYPES
"Gletscher"

BUILDINGTYPE "Monument"
"Monument";name
4;level
RAWMATERIALS
1;Stein
1;Holz
1;Eisen
400;Silber
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

BUILDINGTYPE "Steinkreis"
"Steinkreis";name
2;level
100;maxsize
RAWMATERIALS
5;Stein
5;Holz
REGIONTYPES
"Berge"
"Ebene"
"Gletscher"
"Hochland"
"Sumpf"
"Wüste"
"Wald"
"Vulkan"
"Aktiver Vulkan"

REGIONTYPE "Berge"
"Berge";name
1000;maxworkers
250;roadstones
"true";isAstralVisible
"true";isLand

REGIONTYPE "Ebene"
"Ebene";name
10000;maxworkers
50;roadstones
"true";isAstralVisible
"true";isLand

REGIONTYPE "Gletscher"
"Gletscher";name
100;maxworkers
250;roadstones
"Tunnel";roadsupportbuilding
"true";isAstralVisible
"true";isLand

REGIONTYPE "Hochland"
"Hochland";name
4000;maxworkers
100;roadstones
"true";isAstralVisible
"true";isLand

REGIONTYPE "Sumpf"
"Sumpf";name
2000;maxworkers
75;roadstones
"Damm";roadsupportbuilding
"true";isAstralVisible
"true";isLand

REGIONTYPE "Wüste"
"Wüste";name
500;maxworkers
100;roadstones
"Karawanserei";roadsupportbuilding
"true";isAstralVisible
"true";isLand

REGIONTYPE "Wald"
"Wald";name
10000;maxworkers
50;roadstones
"true";isAstralVisible
"true";isLand

REGIONTYPE "Ozean"
"Ozean";name
0;maxworkers
"true";isOcean
"false";isAstralVisible

FACTION "22"
22;id
"true";isMonster
