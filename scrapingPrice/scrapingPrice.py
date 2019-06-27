from flask import Flask
from flask import request
import requests
import json
from bs4 import BeautifulSoup
app = Flask(__name__)


def find_occ_str(str1, str2):
	c = 0
	str1 = str1.lower()
	str2 = str2.lower()
	len2 = len(str2)
	len1 = len(str1)
	while c < len1:
		c2 = 0
		while c2 < len2 and c < len1 and str1[c] == str2[c2]:
			c2 = c2 + 1
			c = c + 1
		# if (c2 * 100) / len2 >= 100:
		if (c * 100 ) / len1 >= 50 and (len2 * 100) / len1 <= 30:
			return False
		if c2 == len2:
			return True
		c = c + 1
	return False


def fnac_engine(elem, article):
	flag_get = False
	price = 0
	for title in elem.find_all('p'):
		if title.get('class') and title.get('class')[0] == 'Article-desc':
			titleval = title.find('a').get_text().strip()
			if find_occ_str(titleval, article[0]):
				flag_get = True
	if flag_get:
		for money in elem.find_all('span'):
			if money.get('class') and money.get('class')[0] == 'price':
				price = int(money.get_text().strip().split('€')[0])
	if flag_get and price == 0:
		for user_money in elem.find_all('a'):
			if user_money.get('class') and user_money.get('class')[0] == 'userPrice':
				print(titleval)
				price = int(user_money.get_text().strip().split('€')[0])
	return price


def fnac_parse(req_html, article):
	soup = BeautifulSoup(req_html, 'html.parser')
	table_result = []
	for elem in soup.find_all('li'):
		if (elem.get('class') and len(elem.get('class')) > 1 and
					elem.get('class')[1] == 'Article-item'):
			result = fnac_engine(elem, article)
			if result > 0:
				table_result.append(result)
	print("======== END =========")
	return ldlc_engine(table_result, article[1], 1)


def amazone_parse(req_html, article):
	soup = BeautifulSoup(req_html, 'html.parser')
	flag_parse = False
	item = ''
	items = []
	for elem in soup.find_all('span'):
		if elem.get('class') and elem.get('class')[0] == 'a-size-medium':
			if find_occ_str(elem.get_text(), article[0]):
				flag_parse = True
				item = elem.get_text()
		if (flag_parse and elem.get('class') and
					elem.get('class')[0] == 'a-price-whole'):
			print("======== Element ===========")
			print(item)
			print("price : ")
			price = elem.get_text()
			print(price)
			# price = float('.'.join(price.replace(u'\xa0', '').split(',')))
			price = int(price.replace(u'\xa0', '').split(',')[0])
			print(price)
			items.append(price)
			flag_parse = False
	print("amazone Up")
	return ldlc_engine(items, int(article[1]), 1)


def ldlc_engine(elems, bprice, flag):
	if flag == 0:
		for e in elems:
			if e.get('class') and e.get('class')[0] == 'price':
				if e.find('div') is None:
					price = int(e.get_text().replace(" ", '').split('€')[0])
					return (price)
	elif flag == 1:
		nb = 0
		for money in elems:
			nb = nb + money
		if len(elems) > 0:
			print(nb / len(elems))
			if nb / len(elems) > int(bprice):
				return True
		return False


def ldlc_parse(req_html, article):
	soup = BeautifulSoup(req_html, "html.parser")
	flag_better = False
	print(soup.find_all('h1'))
	if soup.find('h1') and find_occ_str(soup.find('h1').get_text(), article[0]):
		flag_better = ldlc_engine(soup.find_all(), article[1])
	else:
		elems = []
		for elem in soup.find_all('li'):
			# print(elem)
			# print(elem.get('class'))
			if elem.get('class') and elem.get('class')[0] == 'pdt-item':
				title = elem.find('h3')
				if find_occ_str(title.find('a').get_text(), article[0]):
					print("======== Elem =======")
					print(title.get_text())
					elems.append(ldlc_engine(elem.find_all('div'), article[1], 0))
		flag_better = ldlc_engine(elems, article[1], 1)
	print('ldlc up')
	return flag_better


@app.route('/analyse', methods=['POST'])
def analyse_init():
	if request.form['title'] and request.form['price']:
		article = [request.form['title'], request.form['price']]
		table_result = []
		c = 0
		# article[0] = '+'.join(article[0].split(' '))
		for link in tableEnter:
			print("Enter Link : " + link + article[0])
			print("====== Payload ==========")
			print(tablePayload[c])
			req = requests.get(link + article[0], headers=tablePayload[c])
			if req.status_code != 200:
				req = requests.get(link + article[0])
			print("init:")
			result = table_func[c](req.text, article)
			if result:
				table_result.append({'match': tablePayload[c]['Host']})
			c = c + 1
		return json.dumps(table_result)
	return 'Bad Gate'


@app.route('/distance', methods={"GET"})
def mesure_distance():
	print(request)
	return 'toto'

def get_payload():
	return {
		'Host': '',
		'User-Agent': 'MesCouillesSurTonNez/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101' +
		'Firefox/60.0',
		 #si casse couille changer ca
		'Accept': 'text/html,application/xhtml+xml',
		'Accept-Language': 'en-US,en;q=0.5',
		'Accept-Encoding': 'gzip, deflate, br',
		'Upgrade-Insecure-Requests': '1',
		'Cache-Control': 'max-age=0, no-cache'
	}


def make_payload(table):
	tpayload = []
	print ("=========== Init Payload ============")
	for elem in table:
		payload = get_payload()
		tmp = elem.split('/')
		for e in tmp:
			if 'www.' in e:
				payload['Host'] = e
		tpayload.append(payload)
	print("END")
	return tpayload


tableEnter = [
	"https://www.amazon.fr/s?k=",
	"https://www.ldlc.com/recherche/",
	'https://www.fnac.com/SearchResult/ResultList.aspx?Search='
]
tablePayload = make_payload(tableEnter)
a = amazone_parse
ldlc = ldlc_parse
fnac = fnac_parse
table_func = [a, ldlc, fnac]
