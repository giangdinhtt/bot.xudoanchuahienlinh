function (keys, values, rereduce) {
    if (rereduce) {
        var count = 0;
        var ids = [];
        values.forEach(function (value) {
            count += value.count;
            ids = ids.concat(value.ids);
        });
        return {
            'ids': ids.slice(0, 10),
            'count': count,
            'dump': values[0]
        }
    } else {
        var ids = [];
        var grades = {};
        var courses = {};
        keys.forEach(function (key) {
            // key format: [key, doc_id]
            ids.push(key[1]);
        });
        values.forEach(function (value) {
            // key format: [grade, course, id]
            if (!grades[value[0]]) grades[value[0]] = 0;
            if (!courses[value[1]]) courses[value[1]] = 0;
            grades[value[0]]++
            courses[value[1]]++
        });
        return {
            'ids': ids,
            'count': values.length,
            'grades': grades,
            'courses': courses
        }
    }
}