<?php
// src/BiddingService.php

class BiddingService {
    public static function inviteVendor($package_id, $vendor_id, $lang = 'en') {
        $pdo = Database::connect();
        
        // Simulation: Send bilingual email
        $msg = ($lang === 'es') 
            ? "Has sido invitado a licitar para el paquete #$package_id." 
            : "You have been invited to bid for package #$package_id.";
        
        error_log("Sending Bidding Invitation: $msg");
        
        ActivityLog::log('System', 'Invited Vendor #'.$vendor_id.' to Bid', 'Invitó al Proveedor #'.$vendor_id.' a licitar', $package_id, 'bidding');
        return true;
    }

    public static function awardBid($bid_id, $lang = 'en') {
        // Simulation: Send award notice
        $msg = ($lang === 'es') ? "¡Felicidades! Su oferta ha sido aceptada." : "Congratulations! Your bid has been accepted.";
        error_log("Award Notice: $msg");
        ActivityLog::log('System', 'Awarded Bid #'.$bid_id, 'Adjudicó la Oferta #'.$bid_id, $bid_id, 'bidding');
        return true;
    }

    public static function rejectBid($bid_id, $lang = 'en') {
        // Simulation: Send rejection notice
        $msg = ($lang === 'es') ? "Gracias por su interés, pero hemos seleccionado otra oferta." : "Thank you for your interest, but we have selected another bid.";
        error_log("Rejection Notice: $msg");
        return true;
    }
}
