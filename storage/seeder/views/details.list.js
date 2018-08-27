function(head, req) {
  var headers = {'Content-Type': 'application/json'};
  var result;
  if(req.query.include_docs != 'true') {
    start({'code': 400, headers: headers});
    result = {'error': 'parameter include_docs=true is required'};
  } else {
    start({'headers': headers});
    result = {};
    while(row = getRow()) {
      var doc = row.doc;
      if (doc.object_type == 'student') {
        result = doc;
      } else if (doc.object_type == 'course') {
        result['course'] = doc;
      } else if (doc.object_type == 'grade') {
        result['grade'] = doc;
      }
    }
  }
  send(JSON.stringify(result));
}