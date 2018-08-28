function (doc) {
    if (doc.object_type == undefined) return;
    if (doc.object_type != 'teacher') return;
    emit(doc._id, 1);
    if (doc.code) emit(doc.code, 1);
    if (doc.phone) emit(doc.phone, 1);
    if (doc.telegram) emit(doc.telegram, 1);
    if (doc.email) {
        emit(doc.email, 1);
        var emailParts = doc.email.split('@');
        if (emailParts.length > 1) emit(emailParts[0], 1);
    }
}
