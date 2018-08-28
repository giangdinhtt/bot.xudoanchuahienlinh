function (doc) {
    if (doc.object_type != 'student') return;
    if (doc.parent == undefined) return;
    var parent = doc.parent;
    var fullName = phone = email = telegram = null;
    if (parent.mother) {
        fullName = parent.mother.full_name;
        phone = parent.mother.phone;
        email = parent.mother.email;
        telegram = parent.mother.telegram;
        var v = [fullName, phone, email, telegram];
        if (phone) emit(phone, v);
        if (email) emit(email, v);
        if (telegram) emit(telegram, v);
    }

    if (parent.father) {
        fullName = parent.father.full_name;
        phone = parent.father.phone;
        email = parent.father.email;
        telegram = parent.father.telegram;
        var v = [fullName, phone, email, telegram];
        if (phone) emit(phone, v);
        if (email) emit(email, v);
        if (telegram) emit(telegram, v);
    }
}
