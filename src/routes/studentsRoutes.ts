import { Router } from 'express';
import StudentsController from '../controllers/studentsController';

const router = Router();
const studentsController = new StudentsController();

export function setStudentsRoutes(app) {
    app.use('/students', router);
    router.post('/', studentsController.createStudent.bind(studentsController));
    router.get('/:id', studentsController.getStudent.bind(studentsController));
    router.put('/:id', studentsController.updateStudent.bind(studentsController));
    router.delete('/:id', studentsController.deleteStudent.bind(studentsController));
}