<?php

namespace App\Controller;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\CollaborateurRepository;
use Symfony\Bundle\SecurityBundle\Security;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcelController extends AbstractController
{
    #[Route('/excel', name: 'app_excel')]
    public function generateExcel(CollaborateurRepository $collaborateurRepository, Security $security, Request $request): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $collaborateurs = $collaborateurRepository->findAllWithAffaires($user->getId());

        $session = $request->getSession();

        // $session->start();
        // Fonction pour incrémenter la colonne

        $firstDate = $session->get('firstDate');
        $lastDate = $session->get('lastDate');

        $ferie = ['01/01', '01/04', '01/05', '05/08', '09/05', '19/05', '20/05', '14/07', '15/08', '01/11', '11/11', '25/12'];

        // $ferie = ['01/01', '04/01','05/01', '05/08', '05/09', '05/19', '05/20', '07/14' ,'08/15', '11/01', '11/11', '12/25'];

        $weekend = ['Saturday', 'Sunday'];


        if (isset($firstDate)) {
            $dateDebut = clone $firstDate; // Use clone to create a new instance
        } else {
            $dateDebut = new DateTime('2024-01-01');
        }
        
        if (isset($lastDate)) {
            $dateFin = clone $lastDate; // Use clone to create a new instance
        } else {
            $dateFin = new DateTime('2024-12-30');
        }




        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(13);
        $sheet->getColumnDimension('C')->setWidth(12.9);
        $sheet->getColumnDimension('D')->setWidth(55.5);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('F')->setWidth(9.5);
        $sheet->getColumnDimension('G')->setWidth(6.5);
        $sheet->getColumnDimension('H')->setWidth(9.5);
        $sheet->getColumnDimension('I')->setWidth(9.5);
        $sheet->getColumnDimension('J')->setWidth(9.5);


        // Add data to the spreadsheet
        $sheet->mergeCells('A1:A4');
        $sheet->mergeCells('B1:B4');
        $sheet->mergeCells('C1:C4');
        $sheet->mergeCells('D1:D4');
        $sheet->mergeCells('E1:E4');
        $sheet->mergeCells('F1:F4');
        $sheet->mergeCells('G1:G4');
        $sheet->mergeCells('H1:H4');
        $sheet->mergeCells('I1:I4');
        $sheet->mergeCells('J1:J4');

        $sheet->setCellValue("A1", "Numéro d'affaires");
        $sheet->setCellValue("B1", "Collaborateur");
        $sheet->setCellValue("C1", "Client");
        $sheet->setCellValue("D1", "Désignation");
        $sheet->setCellValue("E1", "Nbre d'heure");
        $sheet->setCellValue("F1", "Date de début ou Mise à Jour");
        $sheet->setCellValue("G1", "Heure Passée");
        $sheet->setCellValue("H1", "Date Fin Impératif");
        $sheet->setCellValue("I1", "Nombre(s) de Jour(s) de Fractionnement");
        $sheet->setCellValue("J1", "%Temps Réserve");        
        $sheet->setCellValue("K1", "année");        
        $sheet->setCellValue("K2", "jour semaine");        
        $sheet->setCellValue("K3", "date");
        $sheet->setCellValue("K4", "Hr planning");

        $tableauDates = array();
        while ($dateDebut <= $dateFin) {
            $annee = $dateDebut->format('Y');
            $jourSemaine = $dateDebut->format('l');
            $dateFormatee = $dateDebut->format('d/m');
            $numeroSemaine = $dateDebut->format('W');
            $entier = $dateDebut->format('d/m/Y') ; 

            $tableauDates[] = array(
                'annee' => $annee,
                'jourSemaine' => $jourSemaine,
                'dateFormatee' => $dateFormatee,
                'numeroSemaine' => $numeroSemaine,
                'entier' => $entier
            );

            $dateDebut->modify('+1 day'); // Passage à la prochaine journée
        }

        $row = 5;


        $ligne = 1;
        $col = 12;

        $jourFr = [
            'Monday' => 'Lun',
            'Tuesday' => 'Mar',
            'Wednesday' => 'Mer',
            'Thursday' => 'Jeu',
            'Friday' => 'Ven',
            'Saturday' => 'Sam',
            'Sunday' => 'Dim'
            ];
        foreach($tableauDates as $dateInfo) {
            // Remplir les cellules avec les valeurs
            
            $sheet->setCellValue([$col , $ligne], $dateInfo['annee']);
            $ligne++;
            $sheet->setCellValue([$col , $ligne], $jourFr[$dateInfo['jourSemaine']]);
            $ligne++;
            $sheet->setCellValue([$col , $ligne], $dateInfo['dateFormatee']);
            $ligne++;
            $sheet->setCellValue([$col , $ligne], $dateInfo['numeroSemaine']);


            $col ++; // Passer à la ligne suivante
            $ligne = 1;
            $dateDebut->modify('+1 day'); // Passage à la prochaine journée

        }

        foreach($collaborateurs as $collaborateur){
            foreach($collaborateur->getAffaires() as $affaire){
                $col = 12;
                $affaireDebut = clone $affaire->getDateDebut();
                $affaireDebut = DateTime::createFromFormat('d/m/Y', $affaireDebut->format('d/m/Y'));
                $fractionnement = 0;
                $FinAffaires = clone $affaire->getDateDebut();
                $FinAffaires = DateTime::createFromFormat('d/m/Y', $FinAffaires->format('d/m/Y'));
                $FinAffaires->modify('-1 day');

                //Calculer le nombre de jours
                $nombreJours = ($affaire->getNbreHeure()) / ($collaborateur->getHrSemaine() / $collaborateur->getJourSemaine());

                // Arrondir vers le haut
                $nombreJours = ceil($nombreJours);
                $FinAffaires->modify('+'.$nombreJours .'days');

                foreach($tableauDates as $date){
                    if(in_array($date['jourSemaine'], $weekend) || in_array($date['dateFormatee'], $ferie)){
                        if($jourFr[$date['jourSemaine']] == "Sam"){
                            $sheet->setCellValue([$col, $row], 'Sam' .$affaireDebut->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                            if($affaireDebut <= $dateDebut && $FinAffaires >= $dateDebut){
                                $FinAffaires->modify('+1 day');
                            }
                        }elseif($jourFr[$date['jourSemaine']] == "Dim"){
                            $sheet->setCellValue([$col, $row], 'Dim' .$affaireDebut->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                            if($affaireDebut <= $dateDebut && $FinAffaires >= $dateDebut){
                                $FinAffaires->modify('+1 day');
                            }
                        }elseif(in_array($date['dateFormatee'], $ferie)){
                            $sheet->setCellValue([$col, $row], 'F' .$affaireDebut->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                            if($affaireDebut <= $dateDebut && $FinAffaires >= $dateDebut){
                                $FinAffaires->modify('+1 day');
                            }
                        }                        
                    }else{
                        if($affaireDebut <= $date['dateFormatee'] && $FinAffaires >= $date['dateFormatee']){
                            $sheet->setCellValue([$col, $row], $affaireDebut->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                            $sheet->getStyle([$col, $row] )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff'); 
                        }elseif($FinAffaires <= $date['dateFormatee'] && $affaire->getNbreJourFractionnement() > $fractionnement){
                            $fractionnement++ ;
                            $sheet->setCellValue([$col, $row], $affaireDebut->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                            $sheet->getStyle([$col, $row] )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('c0c0c0'); 
                        }else{
                            $sheet->setCellValue([$col, $row], $affaireDebut->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                        }
                    }
                    $col++;
                }
                $row++;
            }
        }














































        // foreach ($collaborateurs as $collaborateur) {
        //     foreach ($collaborateur->getAffaires() as $affaire) {
                
        //         $col = 12;
        //         if($collaborateur->getCouleur() != null){
        //             $couleurSansHashtag = ltrim($collaborateur->getCouleur(), '#');
        //         }
        //         else{
        //             $couleurSansHashtag = 'FFFFFF';
        //         }
                
        //         $row++;
        //         $annee = substr($affaire->getDateDebut()->format('Y'), -2);
        //         // Utiliser $row pour déterminer la ligne actuelle
        //         $sheet->setCellValue("A" . $row, $affaire->getNumAffaire());
        //         $sheet->setCellValue("B" . $row, $collaborateur->getNom() . " " . $collaborateur->getPrenom());
        //         $sheet->setCellValue("C" . $row, $affaire->getClient());
        //         $sheet->setCellValue("D" . $row, $affaire->getDesignation());
        //         $sheet->setCellValue("E" . $row, $affaire->getNbreHeure());
        //         $sheet->setCellValue("F" . $row, $affaire->getDateDebut()->format('d/m/').$annee);
        //         $sheet->setCellValue("G" . $row, $affaire->getHeurePasse());
        //         $sheet->setCellValue("H" . $row, $affaire->getDatefin());
        //         $sheet->setCellValue("I" . $row, $affaire->getNbreJourFractionnement());
        //         $sheet->setCellValue("J" . $row, $affaire->getPourcentReserve());
        //         $sheet->setCellValue("K" . $row, $affaire->getNbreHeure());
        //         $sheet->getStyle('B'. $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($couleurSansHashtag);
        //         $sheet->getStyle('H'. $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($couleurSansHashtag);
        //         $sheet->getStyle('I'. $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($couleurSansHashtag);
        //         $sheet->getStyle('J'. $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($couleurSansHashtag);
               



        //         $FinAffaires = clone $affaire->getDateDebut();

        //         // Soustraire un jour
        //         $FinAffaires->modify('-1 day');

        //         // Calculer le nombre de jours
        //         $nombreJours = ($affaire->getNbreHeure()) / ($collaborateur->getHrSemaine() / $collaborateur->getJourSemaine());

        //         // Arrondir vers le haut
        //         $nombreJours = ceil($nombreJours);

        //         // Ajouter le nombre de jours à la date
        //         $FinAffaires->modify('+' . $nombreJours . ' days');
        //         $fractionnement = 0 ; 
        //         $col = 12;
        //         foreach($tableauDates as $date) {
        //             if (in_array($date['jourSemaine'], $weekend) || in_array($date['dateFormatee'], $ferie)) {
        //                 if($date['jourSemaine'] == 'Saturday'){
        //                     $sheet->setCellValue([$col, $row], 'Sam' .$affaire->getDateDebut()->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
        //                     if($affaire->getDateDebut() <= $dateDebut && $FinAffaires >= $dateDebut){
        //                         $FinAffaires->modify('+1 day');
        //                     }
        //                 }
        //                 elseif($date['jourSemaine'] == 'Sunday'){
        //                     $sheet->setCellValue([$col, $row], 'Dim' . $affaire->getDateDebut()->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
        //                     if($affaire->getDateDebut() <= $dateDebut && $FinAffaires >= $dateDebut){
        //                         $FinAffaires->modify('+1 day');
        //                     }
        //                 }
        //                 elseif(in_array($date['dateFormatee'], $ferie)){
        //                     $sheet->setCellValue([$col, $row], 'F' . $affaire->getDateDebut()->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
        //                     if($affaire->getDateDebut() <= $dateDebut && $FinAffaires >= $dateDebut){
        //                         $FinAffaires->modify('+1 day');
        //                     }
        //                 }                        
        //             }elseif($affaire->getDateDebut() <= $dateDebut && $FinAffaires >= $dateDebut){
        //                 $sheet->setCellValue([$col, $row], $affaire->getDateDebut()->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
        //                 $sheet->getStyle([$col, $row] )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff'); 
        //             }elseif($FinAffaires < $dateDebut && $fractionnement < $affaire->getNbreJourFractionnement()){                        
        //                 $sheet->setCellValue([$col, $row], $affaire->getDateDebut()->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
        //                 $sheet->getStyle([$col, $row] )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('c0c0c0'); 
        //                 $fractionnement++ ;
        //             }else{
        //                 $sheet->setCellValue([$col, $row], $affaire->getDateDebut()->format('d/m') . ' ' . $date['dateFormatee'] . ' ' . $FinAffaires->format('d/m'));
                        
        //             }

                    


        //             $col++; // Passer à la ligne suivante
                    

        //         }
                // $FinAffaires = $affaire->getDateDebut();

                // // Soustraire un jour
                // $FinAffaires->modify('-1 day');

                // // Calculer le nombre de jours
                // $nombreJours = ($affaire->getNbreHeure()) / ($collaborateur->getHrSemaine() / $collaborateur->getJourSemaine());

                // // Arrondir vers le haut
                // $nombreJours = ceil($nombreJours);

                // // Ajouter le nombre de jours à la date
                // $FinAffaires->modify('+' . $nombreJours . ' days');

                // $fractionnement = 0;

                // $col = 12 ;

                // while ($dateDebut <= $lastDate) {

                //     $annee = $dateDebut->format('Y');
                //     $jourSemaine = $dateDebut->format('l');
                //     $dateFormatee = $dateDebut->format('d/m');
                //     $numeroSemaine = $dateDebut->format('W');
                    
                //     if (in_array($jourSemaine, $weekend) || in_array($dateFormatee, $ferie)) {
                //         if($jourSemaine == 'Saturday'){
                //             $sheet->setCellValue([$col .','. $row], "Sam");
                //             $FinAffaires->modify('+1 day');
                //         }
                //         elseif($jourSemaine == 'Sunday'){
                //             $sheet->setCellValue([$col .','. $row], "Dim");
                //             $FinAffaires->modify('+1 day');
                //         }
                //         else{
                //             $sheet->setCellValue([$col .','. $row], "F");
                //             $FinAffaires->modify('+1 day');
                //         }                        
                //     }elseif($affaire->getDateDebut()->format('d/m/Y') <= $dateDebut->format('d/m/Y') && $FinAffaires->format('d/m/Y') >= $dateDebut->format(d/m/Y)){
                //         $sheet->getStyle([$col.','.$row] )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff'); 
                //     }elseif($FinAffaires < $dateDebut && $fractionnement < $affaire->getNbreJourFractionnement()){
                //         $sheet->getStyle([$col.','.$row] )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('c0c0c0'); 
                //     }

        
        
                //     $col ++; // Passer à la ligne suivante
                //     $dateDebut->modify('+1 day'); // Passage à la prochaine journée
        
                // }
               
            //}

            $row++;
            $col = $col - 1 ;
            $value4 = 'A' . $row ;
            $value5 = Coordinate::stringFromColumnIndex($col);
            $value6 = $value5 . $row;
            $sheet->getStyle($value4.':'.$value6 )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff00'); // Jaune
            $col = $col +1 ;
        // }


        $col = $col - 1;

        $value1 = Coordinate::stringFromColumnIndex($col);
        $value2 = $value1 . $row;
        $value3 = $value1 . 4;

        $i = 12;
        while($i <= $col){
            $valuetemp = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($valuetemp)->setWidth(10); //4.7
            $i++;
        }
        $sheet->getStyle('L4:' . $value3)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('L4:'. $value3)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('000000'); // Jaune


        

        $sheet->getStyle('A1:'.$value2)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A1:'.$value2)->getBorders()->getAllBorders()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));        

        $sheet->getStyle('A1:J'.$row)->getFont()->setSize(9);

        $sheet->getStyle('A1:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('c0c0c0');

        $alignment = $sheet->getStyle('A1:J4')->getAlignment();
        $alignment->setWrapText(true);
        $alignment->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $alignment->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $alignment2 = $sheet->getStyle('L1:' . $value2)->getAlignment();
        $alignment2->setWrapText(false);
        $alignment2->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $alignment2->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


        
        $sheet->getStyle('K4:K'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('c0c0c0');
        
        $sheet->getStyle('L1:'.$value1.'4')->getFont()->setSize(8);

        // Create a new Excel writer
        $writer = new Xlsx($spreadsheet);

        // Save the Excel file to a temporary location
        $tempFilePath = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($tempFilePath);

        


        // Return the Excel file as a response
        $response = new Response(file_get_contents($tempFilePath));
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="example.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        // Delete the temporary file
        unlink($tempFilePath);

        return $response;

        return $this->redirectToRoute('affaire_index');
    }
}
