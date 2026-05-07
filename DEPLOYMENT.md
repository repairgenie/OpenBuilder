# OpenBuilder Deployment Checklist

## Environment Setup
- [ ] Set `GEMINI_API_KEY` in environment variables.
- [ ] Ensure `data/openbuilder.sqlite` is writable.
- [ ] Verify `public/` directory permissions.

## Biblia Compliance
- [ ] Run `php scripts/regen_docs.php` to verify bilingual docs.
- [ ] Run `npm test` (Playwright) to verify bilingual UI.

## Performance & Security
- [ ] Enable `mod_rewrite` for clean URLs (if using Apache).
- [ ] Set `display_errors = 0` in production `index.php`.
- [ ] Verify MFA verification flow.

## Final Sweep
- [ ] Clear `cache/` (if any).
- [ ] Test Mobile Bottom Nav on real device simulation.
- [ ] Verify AI Chatbot responsiveness.

---
**Status**: Ready for Production / Listo para Producción
