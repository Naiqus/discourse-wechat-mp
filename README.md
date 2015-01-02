
Recently I registered a Wechat MP for my Discourse forum [E1zone](www.e1zone.de) and developed some server-side functions to fetch users' information from the Discourse forum. 

MySQL is not necessary since what we need for now is just a table. It could be replaced with SQLite or anything you like.

---

**The implemented functions of the Wechat MP:**

- reply "?" to check usage.
- reply "最新" to get weekly highlights list 
- reply "绑定" to bind Discourse account to Wechat account
- reply "解除绑定" to unbind.
- reply "消息" to check recent forum notifications.

---
## Usage:
1. discourse_wx.php is the listener for Wechat server, it should be set properly.
2. Prepare MySQL database, here is my configuration. Just a table.
```sql
CREATE TABLE `account_binding` (
  `username` varchar(20) NOT NULL,
  `API_key` varchar(128) NOT NULL,
  `index` varchar(8) NOT NULL,
  `openID` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`index`),
  UNIQUE KEY `username` (`username`),
  KEY `API_key` (`API_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```
3. Check the files. Set constants and modify strings according to your own configuration.


## Workflow

* Store Discourse user API key, identification code and username in a database.
* discourse user request for the API key**--->** 
* Administrators generate user API key **--->** 
* Insert the entry which contains information of api_key，Discourse username，and identification code.**--->** 
* send Wechat identification code to user**--->** 
* Detect identification code from user reply **--->** 
* validate the code, update database, add user's wechat OpenID.

