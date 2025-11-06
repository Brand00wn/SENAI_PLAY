// disciplinas.js
// Script de validação e interação da página de Disciplinas
// Local: schoolcontrol-pro/disciplinas/disciplinas.js

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formDisciplina");
    const nomeInput = document.getElementById("nome");
    const cargaInput = document.getElementById("carga");
  
    // Cria um elemento de mensagem para feedback visual
    const mensagem = document.createElement("div");
    mensagem.id = "mensagem";
    mensagem.style.margin = "10px 0";
    document.querySelector(".container").prepend(mensagem);
  
    // Validação de formulário
    form.addEventListener("submit", (e) => {
      const nome = nomeInput.value.trim();
      const carga = parseInt(cargaInput.value, 10);
  
      if (!nome) {
        e.preventDefault();
        mostrarMensagem("⚠️ O nome da disciplina é obrigatório.", "erro");
        nomeInput.focus();
        return;
      }
  
      if (isNaN(carga) || carga <= 0) {
        e.preventDefault();
        mostrarMensagem("⚠️ A carga horária deve ser um número maior que zero.", "erro");
        cargaInput.focus();
        return;
      }
  
      mostrarMensagem("✅ Disciplina cadastrada com sucesso!", "sucesso");
    });
  
    // Confirmação ao clicar em "Excluir"
    document.querySelectorAll("a[href*='excluir']").forEach((link) => {
      link.addEventListener("click", (e) => {
        const confirmar = confirm("Tem certeza que deseja excluir esta disciplina?");
        if (!confirmar) e.preventDefault();
      });
    });
  
    /**
     * Exibe mensagens de feedback para o usuário.
     * @param {string} texto - Texto da mensagem
     * @param {"sucesso"|"erro"} tipo - Tipo de mensagem
     */
    function mostrarMensagem(texto, tipo) {
      mensagem.textContent = texto;
      mensagem.style.padding = "10px";
      mensagem.style.borderRadius = "6px";
      mensagem.style.textAlign = "center";
      mensagem.style.transition = "all 0.3s ease";
  
      if (tipo === "sucesso") {
        mensagem.style.background = "#d4edda";
        mensagem.style.color = "#155724";
        mensagem.style.border = "1px solid #c3e6cb";
      } else {
        mensagem.style.background = "#f8d7da";
        mensagem.style.color = "#721c24";
        mensagem.style.border = "1px solid #f5c6cb";
      }
  
      // Remove mensagem após 3 segundos
      setTimeout(() => {
        mensagem.textContent = "";
        mensagem.style.padding = "";
        mensagem.style.border = "";
        mensagem.style.background = "";
      }, 3000);
    }
  });
  