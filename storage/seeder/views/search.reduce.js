function (keys, values, rereduce) {
    if (rereduce) {
        var count = 0;
        var ids = [];
        values.forEach(function (value) {
            count += value.count;
            ids = ids.concat(value.ids);
        });
        return {
            'ids': ids,
            'count': count,
        }
    } else {
        var ids = [];
        keys.forEach(function (key) {
            // key format: [key, doc_id]
            ids.push(key[1]);
        });
        return {
            'ids': ids,
            'count': values.length,
        }
    }
}