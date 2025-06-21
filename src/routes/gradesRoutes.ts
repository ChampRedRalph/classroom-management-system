import { Router } from 'express';
import GradesController from '../controllers/gradesController';

const router = Router();
const gradesController = new GradesController();

router.post('/grades', gradesController.addGrade.bind(gradesController));
router.get('/grades/:studentId', gradesController.getGrades.bind(gradesController));
router.put('/grades/:studentId', gradesController.updateGrade.bind(gradesController));

export default function setGradesRoutes(app) {
    app.use('/api', router);
}