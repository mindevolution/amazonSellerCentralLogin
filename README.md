# amazonSellerCentralLogin
Use php curl login amazon sellercentral.amazon.com

When login, then you can navigate to other pages such as download report files.

 # Exampel, get the report page 

 $url = "https://sellercentral.amazon.com/gp/site-metrics/report.html#&cols=/c0/c1/c2/c3/c4/c5/c6/c7/c8/c9/c10/c11/c12&sortColumn=13&filterFromDate=05/10/2015&filterToDate=06/10/2015&fromDate=05/10/2015&toDate=06/10/2015&reportID=102:DetailSalesTrafficBySKU&sortIsAscending=0&currentPage=0&dateUnit=1&viewDateUnits=ALL&runDate=";
 $report = Login::getAmazonBackendUrl(Login::loginSellercentral($username, $password), $url);
