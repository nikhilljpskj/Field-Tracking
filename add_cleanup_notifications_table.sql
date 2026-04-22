-- =========================================================
-- CLEANUP NOTIFICATION DISMISSAL TRACKING
-- Run on both dev and production. Safe (IF NOT EXISTS).
-- =========================================================

CREATE TABLE IF NOT EXISTS cleanup_notification_dismissals (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    dismiss_date DATE NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_date (user_id, dismiss_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
