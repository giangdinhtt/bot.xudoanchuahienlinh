function (doc) {
    if (doc.telegram_id) emit(doc.telegram_id, 1);
    if (doc.telegram) emit(doc.telegram, 1);
}
