Impor / reset data charger dari CSV
===============================
Sumber: data_charger.csv
Total baris: 2222
Master unik (merk+tipe): 258
Baris dengan serial terdeteksi: 2069
Baris dengan unit terdeteksi: 1238

Urutan eksekusi:
  1) import_charger_master.sql
  2) import_inventory_chargers.sql

Mapping status:
  TERPASANG -> IN_USE
  AVAILABLE -> AVAILABLE
  SPARE -> SPARE
  DIJUAL -> SOLD

Distribusi status hasil:
  IN_USE: 1134
  AVAILABLE: 606
  SPARE: 328
  SOLD: 154

Catatan:
- `CHARGER ` mentah diparse menjadi merk, tipe, serial, voltage, ampere.
- Data sulit diparse tetap disimpan di kolom notes (Charger Raw).
- Format NO CHARGER non-standar dinormalisasi + dicatat di warning.

Warning (maks 250):
Baris ~127: item_number duplikat -> C0206-DUP2
Baris ~161: item_number duplikat -> C0239-DUP2
Baris ~163: item_number duplikat -> C0240-DUP2
Baris ~166: item_number duplikat -> C0242-DUP2
Baris ~381: item_number duplikat -> C0456-DUP2
Baris ~1470: Format NO CHARGER '1464C' dinormalisasi
Baris ~1470: item_number duplikat -> C1464-DUP2
Baris ~1471: Format NO CHARGER '1465C' dinormalisasi
Baris ~1471: item_number duplikat -> C1465-DUP2
Baris ~1472: Format NO CHARGER '1466C' dinormalisasi
Baris ~1472: item_number duplikat -> C1466-DUP2
Baris ~1473: Format NO CHARGER '1467C' dinormalisasi
Baris ~1473: item_number duplikat -> C1467-DUP2
Baris ~1474: Format NO CHARGER '1468C' dinormalisasi
Baris ~1474: item_number duplikat -> C1468-DUP2
Baris ~1475: Format NO CHARGER '1469C' dinormalisasi
Baris ~1475: item_number duplikat -> C1469-DUP2
Baris ~1476: Format NO CHARGER '1470C' dinormalisasi
Baris ~1476: item_number duplikat -> C1470-DUP2
Baris ~1477: Format NO CHARGER '1471C' dinormalisasi
Baris ~1477: item_number duplikat -> C1471-DUP2
Baris ~1478: Format NO CHARGER '1472C' dinormalisasi
Baris ~1478: item_number duplikat -> C1472-DUP2
Baris ~1479: Format NO CHARGER '1473C' dinormalisasi
Baris ~1479: item_number duplikat -> C1473-DUP2
Baris ~1480: Format NO CHARGER '1474C' dinormalisasi
Baris ~1480: item_number duplikat -> C1474-DUP2
Baris ~1481: Format NO CHARGER '1475C' dinormalisasi
Baris ~1481: item_number duplikat -> C1475-DUP2
Baris ~1482: Format NO CHARGER '1476C' dinormalisasi
Baris ~1482: item_number duplikat -> C1476-DUP2
Baris ~1483: Format NO CHARGER '1477C' dinormalisasi
Baris ~1483: item_number duplikat -> C1477-DUP2
Baris ~1484: Format NO CHARGER '1478C' dinormalisasi
Baris ~1484: item_number duplikat -> C1478-DUP2
Baris ~1485: Format NO CHARGER '1479C' dinormalisasi
Baris ~1485: item_number duplikat -> C1479-DUP2
Baris ~1486: Format NO CHARGER '1480C' dinormalisasi
Baris ~1486: item_number duplikat -> C1480-DUP2
Baris ~1487: Format NO CHARGER '1481C' dinormalisasi
Baris ~1487: item_number duplikat -> C1481-DUP2
Baris ~1488: Format NO CHARGER '1482C' dinormalisasi
Baris ~1488: item_number duplikat -> C1482-DUP2
Baris ~1489: Format NO CHARGER '1483C' dinormalisasi
Baris ~1489: item_number duplikat -> C1483-DUP2
Baris ~1490: Format NO CHARGER '1484C' dinormalisasi
Baris ~1490: item_number duplikat -> C1484-DUP2
Baris ~1491: Format NO CHARGER '1485C' dinormalisasi
Baris ~1491: item_number duplikat -> C1485-DUP2
Baris ~1492: Format NO CHARGER '1486C' dinormalisasi
Baris ~1492: item_number duplikat -> C1486-DUP2
Baris ~1493: Format NO CHARGER '1487C' dinormalisasi
Baris ~1493: item_number duplikat -> C1487-DUP2
Baris ~1494: Format NO CHARGER '1488C' dinormalisasi
Baris ~1494: item_number duplikat -> C1488-DUP2
Baris ~1495: Format NO CHARGER '1489C' dinormalisasi
Baris ~1495: item_number duplikat -> C1489-DUP2
Baris ~1496: Format NO CHARGER '1490C' dinormalisasi
Baris ~1496: item_number duplikat -> C1490-DUP2
Baris ~1497: Format NO CHARGER '1491C' dinormalisasi
Baris ~1497: item_number duplikat -> C1491-DUP2
Baris ~1498: Format NO CHARGER '1492C' dinormalisasi
Baris ~1498: item_number duplikat -> C1492-DUP2
Baris ~1499: Format NO CHARGER '1493C' dinormalisasi
Baris ~1499: item_number duplikat -> C1493-DUP2
Baris ~1500: Format NO CHARGER '1494C' dinormalisasi
Baris ~1500: item_number duplikat -> C1494-DUP2
Baris ~1501: Format NO CHARGER '1495C' dinormalisasi
Baris ~1501: item_number duplikat -> C1495-DUP2
Baris ~1502: Format NO CHARGER '1496C' dinormalisasi
Baris ~1502: item_number duplikat -> C1496-DUP2
Baris ~1503: Format NO CHARGER '1497C' dinormalisasi
Baris ~1503: item_number duplikat -> C1497-DUP2
Baris ~1504: Format NO CHARGER '1498C' dinormalisasi
Baris ~1504: item_number duplikat -> C1498-DUP2
Baris ~1505: Format NO CHARGER '1499C' dinormalisasi
Baris ~1505: item_number duplikat -> C1499-DUP2
Baris ~1506: Format NO CHARGER '1500C' dinormalisasi
Baris ~1506: item_number duplikat -> C1500-DUP2
Baris ~1507: Format NO CHARGER '1501C' dinormalisasi
Baris ~1507: item_number duplikat -> C1501-DUP2
Baris ~1508: Format NO CHARGER '1502C' dinormalisasi
Baris ~1508: item_number duplikat -> C1502-DUP2
Baris ~1509: Format NO CHARGER '1503C' dinormalisasi
Baris ~1509: item_number duplikat -> C1503-DUP2
Baris ~1510: Format NO CHARGER '1504C' dinormalisasi
Baris ~1510: item_number duplikat -> C1504-DUP2
Baris ~1511: Format NO CHARGER '1505C' dinormalisasi
Baris ~1511: item_number duplikat -> C1505-DUP2
Baris ~1512: Format NO CHARGER '1506C' dinormalisasi
Baris ~1512: item_number duplikat -> C1506-DUP2
Baris ~1513: Format NO CHARGER '1507C' dinormalisasi
Baris ~1513: item_number duplikat -> C1507-DUP2
Baris ~1514: Format NO CHARGER '1508C' dinormalisasi
Baris ~1514: item_number duplikat -> C1508-DUP2
Baris ~1515: Format NO CHARGER '1509C' dinormalisasi
Baris ~1515: item_number duplikat -> C1509-DUP2
Baris ~1516: Format NO CHARGER '1510C' dinormalisasi
Baris ~1516: item_number duplikat -> C1510-DUP2
Baris ~1517: Format NO CHARGER '1511C' dinormalisasi
Baris ~1517: item_number duplikat -> C1511-DUP2
Baris ~1518: Format NO CHARGER '1512C' dinormalisasi
Baris ~1518: item_number duplikat -> C1512-DUP2
Baris ~1519: Format NO CHARGER '1513C' dinormalisasi
Baris ~1519: item_number duplikat -> C1513-DUP2
Baris ~1520: Format NO CHARGER '1514C' dinormalisasi
Baris ~1520: item_number duplikat -> C1514-DUP2
Baris ~1521: Format NO CHARGER '1515C' dinormalisasi
Baris ~1521: item_number duplikat -> C1515-DUP2
Baris ~1522: Format NO CHARGER '1516C' dinormalisasi
Baris ~1522: item_number duplikat -> C1516-DUP2
Baris ~1523: Format NO CHARGER '1517C' dinormalisasi
Baris ~1523: item_number duplikat -> C1517-DUP2
Baris ~1524: Format NO CHARGER '1518C' dinormalisasi
Baris ~1524: item_number duplikat -> C1518-DUP2
Baris ~1525: Format NO CHARGER '1519C' dinormalisasi
Baris ~1525: item_number duplikat -> C1519-DUP2
Baris ~1526: Format NO CHARGER '1520C' dinormalisasi
Baris ~1526: item_number duplikat -> C1520-DUP2
Baris ~1527: Format NO CHARGER '1521C' dinormalisasi
Baris ~1527: item_number duplikat -> C1521-DUP2
Baris ~1528: Format NO CHARGER '1522C' dinormalisasi
Baris ~1528: item_number duplikat -> C1522-DUP2
Baris ~1529: Format NO CHARGER '1523C' dinormalisasi
Baris ~1529: item_number duplikat -> C1523-DUP2
Baris ~1530: Format NO CHARGER '1524C' dinormalisasi
Baris ~1530: item_number duplikat -> C1524-DUP2
Baris ~1531: Format NO CHARGER '1525C' dinormalisasi
Baris ~1531: item_number duplikat -> C1525-DUP2
Baris ~1532: Format NO CHARGER '1526C' dinormalisasi
Baris ~1532: item_number duplikat -> C1526-DUP2
Baris ~1533: Format NO CHARGER '1527C' dinormalisasi
Baris ~1533: item_number duplikat -> C1527-DUP2
Baris ~1534: Format NO CHARGER '1528C' dinormalisasi
Baris ~1534: item_number duplikat -> C1528-DUP2
Baris ~1535: Format NO CHARGER '1529C' dinormalisasi
Baris ~1535: item_number duplikat -> C1529-DUP2
Baris ~1536: Format NO CHARGER '1530C' dinormalisasi
Baris ~1536: item_number duplikat -> C1530-DUP2
Baris ~1537: Format NO CHARGER '1531C' dinormalisasi
Baris ~1537: item_number duplikat -> C1531-DUP2
Baris ~1538: Format NO CHARGER '1532C' dinormalisasi
Baris ~1538: item_number duplikat -> C1532-DUP2
Baris ~1539: Format NO CHARGER '1533C' dinormalisasi
Baris ~1539: item_number duplikat -> C1533-DUP2
Baris ~1540: Format NO CHARGER '1534C' dinormalisasi
Baris ~1540: item_number duplikat -> C1534-DUP2
Baris ~1541: Format NO CHARGER '1535C' dinormalisasi
Baris ~1541: item_number duplikat -> C1535-DUP2
Baris ~1542: Format NO CHARGER tidak standar: 'CL01'
Baris ~1543: Format NO CHARGER tidak standar: 'CL02'
Baris ~1544: Format NO CHARGER tidak standar: 'CL03'
Baris ~1545: Format NO CHARGER tidak standar: 'CL04'
Baris ~1546: Format NO CHARGER tidak standar: 'CL05'
Baris ~1547: Format NO CHARGER tidak standar: 'CL06'
Baris ~1548: Format NO CHARGER tidak standar: 'CL07'
Baris ~1549: Format NO CHARGER tidak standar: 'CL08'
Baris ~1550: Format NO CHARGER tidak standar: 'CL09'
Baris ~1551: Format NO CHARGER tidak standar: 'CL10'
Baris ~1552: Format NO CHARGER tidak standar: 'CL11'
Baris ~1553: Format NO CHARGER tidak standar: 'CL12'
Baris ~1554: Format NO CHARGER tidak standar: 'CL13'
Baris ~1555: Format NO CHARGER tidak standar: 'CL14'
Baris ~1556: Format NO CHARGER tidak standar: 'CL15'
Baris ~1557: Format NO CHARGER tidak standar: 'CL16'
Baris ~1558: Format NO CHARGER tidak standar: 'CL17'
Baris ~1559: Format NO CHARGER tidak standar: 'CL18'
Baris ~1560: Format NO CHARGER tidak standar: 'CL19'
Baris ~1561: Format NO CHARGER tidak standar: 'CL20'
Baris ~1562: Format NO CHARGER tidak standar: 'CL21'
Baris ~1563: Format NO CHARGER tidak standar: 'CL22'
Baris ~1564: Format NO CHARGER tidak standar: 'CL23'
Baris ~1565: Format NO CHARGER tidak standar: 'CL24'
Baris ~1566: Format NO CHARGER tidak standar: 'CL25'
Baris ~1567: Format NO CHARGER tidak standar: 'CL26'
Baris ~1568: Format NO CHARGER tidak standar: 'CL27'
Baris ~1569: Format NO CHARGER tidak standar: 'CL28'
Baris ~1570: Format NO CHARGER tidak standar: 'CL29'
Baris ~1571: Format NO CHARGER tidak standar: 'CL30'
Baris ~1572: Format NO CHARGER tidak standar: 'CL31'
Baris ~1573: Format NO CHARGER tidak standar: 'CL32'
Baris ~1574: Format NO CHARGER tidak standar: 'CL33'
Baris ~1575: Format NO CHARGER tidak standar: 'CL34'
Baris ~1576: Format NO CHARGER tidak standar: 'CL35'
Baris ~1577: Format NO CHARGER tidak standar: 'CL36'
Baris ~1578: Format NO CHARGER tidak standar: 'CL37'
Baris ~1579: Format NO CHARGER tidak standar: 'CL38'
Baris ~1580: Format NO CHARGER tidak standar: 'CL39'
Baris ~1581: Format NO CHARGER tidak standar: 'CL40'
Baris ~1582: Format NO CHARGER tidak standar: 'CL41'
Baris ~1583: Format NO CHARGER tidak standar: 'CL42'
Baris ~1584: Format NO CHARGER tidak standar: 'CL43'
Baris ~1585: Format NO CHARGER tidak standar: 'CL44'
Baris ~1586: Format NO CHARGER tidak standar: 'CL45'
Baris ~1587: Format NO CHARGER tidak standar: 'CL46'
Baris ~1588: Format NO CHARGER tidak standar: 'CL47'
Baris ~1589: Format NO CHARGER tidak standar: 'CL48'
Baris ~1590: Format NO CHARGER tidak standar: 'CL49'
Baris ~1591: Format NO CHARGER tidak standar: 'CL50'
Baris ~1592: Format NO CHARGER tidak standar: 'CL51'
Baris ~1593: Format NO CHARGER tidak standar: 'CL52'
Baris ~1594: Format NO CHARGER tidak standar: 'CL53'
Baris ~1595: Format NO CHARGER tidak standar: 'CL54'
Baris ~1596: Format NO CHARGER tidak standar: 'CL55'
Baris ~1597: Format NO CHARGER tidak standar: 'CL56'
Baris ~1598: Format NO CHARGER tidak standar: 'CL57'
Baris ~1599: Format NO CHARGER tidak standar: 'CL58'
Baris ~1600: Format NO CHARGER tidak standar: 'CL59'
Baris ~1601: Format NO CHARGER tidak standar: 'CL60'
Baris ~1602: Format NO CHARGER tidak standar: 'CL61'
Baris ~1603: Format NO CHARGER tidak standar: 'CL62'
Baris ~1604: Format NO CHARGER tidak standar: 'CL63'
Baris ~1605: Format NO CHARGER tidak standar: 'CL64'
Baris ~1606: Format NO CHARGER tidak standar: 'CL65'
Baris ~1607: Format NO CHARGER tidak standar: 'CL66'
Baris ~1608: Format NO CHARGER tidak standar: 'CL67'
Baris ~1609: Format NO CHARGER tidak standar: 'CL68'
Baris ~1610: Format NO CHARGER tidak standar: 'CL69'
Baris ~1611: Format NO CHARGER tidak standar: 'CL70'
Baris ~1612: Format NO CHARGER tidak standar: 'CL71'
Baris ~1613: Format NO CHARGER tidak standar: 'CL72'
Baris ~1614: Format NO CHARGER tidak standar: 'CL73'
Baris ~1615: Format NO CHARGER tidak standar: 'CL74'
Baris ~1616: Format NO CHARGER tidak standar: 'CL75'
Baris ~1617: Format NO CHARGER tidak standar: 'CL76'
Baris ~1618: Format NO CHARGER tidak standar: 'CL77'
Baris ~1619: Format NO CHARGER tidak standar: 'CL78'
Baris ~1620: Format NO CHARGER tidak standar: 'CL79'
Baris ~1621: Format NO CHARGER tidak standar: 'CL80'
Baris ~1622: Format NO CHARGER tidak standar: 'CL81'
Baris ~1623: Format NO CHARGER tidak standar: 'CL82'
Baris ~1624: Format NO CHARGER tidak standar: 'CL83'
Baris ~1625: Format NO CHARGER tidak standar: 'CL84'
Baris ~1626: Format NO CHARGER tidak standar: 'CL85'
Baris ~1627: Format NO CHARGER tidak standar: 'CL86'
Baris ~1628: Format NO CHARGER tidak standar: 'CL87'
Baris ~1629: Format NO CHARGER tidak standar: 'CL88'
Baris ~1630: Format NO CHARGER tidak standar: 'CL89'
Baris ~1631: Format NO CHARGER tidak standar: 'CL90'
Baris ~1632: Format NO CHARGER tidak standar: 'CL91'
Baris ~1633: Format NO CHARGER tidak standar: 'CL92'
Baris ~1634: Format NO CHARGER tidak standar: 'CL93'
Baris ~1635: Format NO CHARGER tidak standar: 'CL94'
Baris ~1636: Format NO CHARGER tidak standar: 'CL95'
Baris ~1637: Format NO CHARGER tidak standar: 'CL96'
Baris ~1638: Format NO CHARGER tidak standar: 'CL97'
Baris ~1639: Format NO CHARGER tidak standar: 'CL98'
Baris ~1640: Format NO CHARGER tidak standar: 'CL99'
Baris ~1641: Format NO CHARGER tidak standar: 'CL100'
Baris ~1642: Format NO CHARGER tidak standar: 'CL101'
... dan 581 warning lainnya