
Recently I registered a Wechat MP for my Discourse forum [E1zone](www.e1zone.de) and developed some server-side functions to fetch users' information from the Discourse forum. 

The usage of MySQL is not necessary since what we need for now is just a table.

---

**The implemented functions of the Wechat MP:**

- reply "?" to check usage.
- reply "最新" to get weekly highlights list 
- reply "绑定" to bind Discourse account to Wechat account
- reply "解除绑定" to unbind.
- reply "消息" to check recent forum notifications.

## Binding Wechat OpenID to Discourse User Account

* Store Discourse user API key, identification code and username in a database.
* discourse user request for the API key**--->** 
* Administrators generate user API key **--->** 
* set a snippet of API key as wechat identification code **--->** 
* Insert the entry which contains information of api_key，Discourse username，and identification code.**--->** 
* Detect identification code from user reply **--->** 
* validate the code, update database, add user's wechat OpenID.

