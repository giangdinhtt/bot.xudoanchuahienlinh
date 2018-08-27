function (keys, values, rereduce) {
    if (rereduce) {
        var stats = values.reduce(function (acc, curr) {
            acc.count += curr.count;
            acc.ids = acc.ids.concat(curr.ids);
            var grades = curr.grades ? curr.grades : {};
            var accGrade = currGrade = accCourse = currCourse = null;
            Object.keys(grades).forEach(function(key) {
                if (key == 'count') return;
                if (!acc.grades[key]) acc.grades[key] = {'count': 0, 'courses': {}};
                accGrade = acc.grades[key];
                currGrade = grades[key];
                accGrade.count += currGrade.count;

                Object.keys(currGrade.courses).forEach(function(courseId) {
                    if (courseId == 'count') return;
                    if (!accGrade.courses[courseId]) accGrade.courses[courseId] = {'count': 0, 'ids': []};
                    accCourse = accGrade.courses[courseId];
                    currCourse = currGrade.courses[courseId];
                    accCourse.count += currCourse.count;
                    accCourse.ids = accCourse.ids.concat(currGrade.courses[courseId].ids);
                });
            });

            return acc;
        }, {'count': 0, 'ids': [], 'grades': {}});
        if (stats.ids.length > 5) stats.ids = stats.ids.slice(0, 5);
        return stats;
    } else {
        var ids = [];
        var grades = {};
        var courses = {};
        keys.forEach(function (key) {
            // key format: [key, doc_id]
            ids.push(key[1]);
        });
        // value format: [grade, course, id, doc_id]
        var stats = values.reduce(function (acc, curr) {
            var gradeId = curr[0];
            var courseId = curr[1];
            if (!acc.grades[gradeId]) acc.grades[gradeId] = {'count': 0, 'courses': {}};
            var grade = acc.grades[gradeId];
            if (!grade.courses[courseId]) grade.courses[courseId] = {'count': 0, 'ids': []};
            var course = grade.courses[courseId];
            grade.count++;
            course.count++;
            course.ids.push(curr[3]);

            return acc;
        }, {'count': values.length, 'ids': ids, 'grades': {}});
        return stats;
    }
}