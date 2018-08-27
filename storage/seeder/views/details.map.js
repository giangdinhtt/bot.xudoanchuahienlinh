function (doc) {
  emit(doc._id, {_id: doc._id});
  if (doc.object_type == "student") {
      emit([doc._id, 0], doc);
      emit([doc._id, 1], {_id: doc.course});
      emit([doc._id, 2], {_id: doc.grade});
  }
}