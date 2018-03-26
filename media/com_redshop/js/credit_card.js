function IsNumeric(e){var r,t="0123456789",a=!0;if(0==e.length)return!1;for(i=0;i<e.length&&1==a;i++)r=e.charAt(i),t.indexOf(r)==-1&&(a=!1);return a}function isString(e){for(var r,t=e.value,a=t.length,n=". -,",i=0;i!=a;i++)if(aChar=t.substring(i,i+1),aChar=aChar.toUpperCase(),r=n.indexOf(aChar),r==-1&&(aChar<"A"||aChar>"Z"))return!1;return!0}function CheckCardNumber(form){if(""!=jQuery('input:radio[name="selectedCard"]:checked').val())return!0;var tmpyear;if(form.order_payment_name.value.length>0&&0==isString(form.order_payment_name))return alert("Please do not enter Numeric value in card name."),form.order_payment_name.focus(),!1;if(0==form.order_payment_number.value.length)return alert("Please enter a Card Number."),form.order_payment_number.focus(),!1;if(0==IsNumeric(form.order_payment_number.value))return alert("Please enter Numeric value only in card number."),form.order_payment_number.focus(),!1;if(tmpyear=form.order_payment_expire_year.options[form.order_payment_expire_year.selectedIndex].value,tmpmonth=form.order_payment_expire_month.options[form.order_payment_expire_month.selectedIndex].value,!(new CardType).isExpiryDate(tmpyear,tmpmonth))return alert("This card has already expired."),!1;if(card=!1,"undefined"!=typeof form.creditcard_code)if(cardlen="undefined"!=typeof form.creditcard_code.type?1:form.creditcard_code.length,cardlen>1)for(var c=0;c<cardlen;c++)form.creditcard_code[c].checked&&(card=form.creditcard_code[c].value);else form.creditcard_code.checked&&(card=form.creditcard_code);if(!card)return alert("Please Select Credit Card type"),!1;var retval=eval(card+'.checkCardNumber("'+form.order_payment_number.value+'", '+tmpyear+", "+tmpmonth+");");return cardname="",retval?!(!isNum(form.credit_card_code.value)||""==trim(form.credit_card_code.value)||"amex"==card&&4!=form.credit_card_code.value.length||"amex"!=card&&3!=form.credit_card_code.value.length)||(alert("Enter vaild Security Number."),!1):(alert("This card number is not valid."),!1)}function CardType(){var e=CardType.arguments,r=CardType.arguments.length;this.objname="object CardType";var t=r>0?e[0]:"CardObject",a=r>1?e[1]:"0,1,2,3,4,5,6,7,8,9",n=r>2?e[2]:"13,14,15,16,19";return this.setCardNumber=setCardNumber,this.setCardType=setCardType,this.setLen=setLen,this.setRules=setRules,this.setExpiryDate=setExpiryDate,this.setCardType(t),this.setLen(n),this.setRules(a),r>4&&this.setExpiryDate(e[3],e[4]),this.checkCardNumber=checkCardNumber,this.getExpiryDate=getExpiryDate,this.getCardType=getCardType,this.isCardNumber=isCardNumber,this.isExpiryDate=isExpiryDate,this.luhnCheck=luhnCheck,this}function checkCardNumber(){var e=checkCardNumber.arguments,r=checkCardNumber.arguments.length,t=r>0?e[0]:this.cardnumber,a=r>1?e[1]:this.year,n=r>2?e[2]:this.month;return this.setCardNumber(t),this.setExpiryDate(a,n),!!this.isCardNumber()&&!!this.isExpiryDate()}function getCardType(){return this.cardtype}function getExpiryDate(){return this.month+"/"+this.year}function isCardNumber(){var e=isCardNumber.arguments,r=isCardNumber.arguments.length,t=r>0?e[0]:this.cardnumber;if(!this.luhnCheck())return!1;for(var a=0;a<this.len.size;a++)if(t.toString().length==this.len[a]){for(var n=0;n<this.rules.size;n++){var i=t.substring(0,this.rules[n].toString().length);if(i==this.rules[n])return!0}return!1}return!1}function isExpiryDate(){var e=isExpiryDate.arguments,r=isExpiryDate.arguments.length;return year=r>0?e[0]:this.year,month=r>1?e[1]:this.month,!!isNum(year+"")&&(!!isNum(month+"")&&(today=new Date,expiry=new Date(year,month),!(today.getTime()>expiry.getTime())))}function isNum(e){if(e=e.toString(),0==e.length)return!1;for(var r=0;r<e.length;r++)if(e.substring(r,r+1)<"0"||e.substring(r,r+1)>"9")return!1;return!0}function luhnCheck(){var e=luhnCheck.arguments,r=luhnCheck.arguments.length,t=r>0?e[0]:this.cardnumber;if(!isNum(t))return!1;for(var a=t.length,n=1&a,i=0,s=0;s<a;s++){var d=parseInt(t.charAt(s));1&s^n||(d*=2,d>9&&(d-=9)),i+=d}return i%10==0}function makeArray(e){return this.size=e,this}function setCardNumber(e){return this.cardnumber=e,this}function setCardType(e){return this.cardtype=e,this}function setExpiryDate(e,r){return this.year=e,this.month=r,this}function setLen(e){0!=e.length&&null!=e||(e="13,14,15,16,19");var r=e;for(n=1;r.indexOf(",")!=-1;)r=r.substring(r.indexOf(",")+1,r.length),n++;for(this.len=new makeArray(n),n=0;e.indexOf(",")!=-1;){var t=e.substring(0,e.indexOf(","));this.len[n]=t,e=e.substring(e.indexOf(",")+1,e.length),n++}return this.len[n]=e,this}function setRules(e){0!=e.length&&null!=e||(e="0,1,2,3,4,5,6,7,8,9");var r=e;for(n=1;r.indexOf(",")!=-1;)r=r.substring(r.indexOf(",")+1,r.length),n++;for(this.rules=new makeArray(n),n=0;e.indexOf(",")!=-1;){var t=e.substring(0,e.indexOf(","));this.rules[n]=t,e=e.substring(e.indexOf(",")+1,e.length),n++}return this.rules[n]=e,this}var Cards=new makeArray(8);Cards[0]=new CardType("MasterCard","51,52,53,54,55","16");var MC=Cards[0],maestro=Cards[0];Cards[1]=new CardType("VisaCard","4","13,16");var VISA=Cards[1];Cards[2]=new CardType("AmExCard","34,37","15");var amex=Cards[2];Cards[3]=new CardType("DinersClubCard","30,36,38","14");var diners=Cards[3];Cards[4]=new CardType("DiscoverCard","6011","16");var DiscoverCard=Cards[4];Cards[5]=new CardType("enRouteCard","2014,2149","15");var enRouteCard=Cards[5];Cards[6]=new CardType("JCBCard","3088,3096,3112,3158,3337,3528","16");var jcb=Cards[6],LuhnCheckSum=Cards[7]=new CardType;