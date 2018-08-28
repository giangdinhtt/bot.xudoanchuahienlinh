function (doc) {
    emit(doc._id, 1);
    if (doc.code) emit(doc.code, 1);
    if (doc.phone) emit(doc.phone, 1);
    if (doc.email) emit(doc.email, 1);
    if (doc.telegram) emit(doc.telegram, 1);
    if (doc.telegram_username) emit(doc.telegram_username, 1);
}
