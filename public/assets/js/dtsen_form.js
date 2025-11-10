/**
 * DTSEN Multi-Step Form Controller (v2)
 * By Rian x Katie ❤️
 */

document.addEventListener("DOMContentLoaded", () => {
  // === Variabel dasar ===
  let currentStep = 0;
  const totalSteps = 7;
  const progressBar = document.getElementById("progress-bar");
  const formElement = document.getElementById("formDtsen");
  let currentUsulanId = null;

  const btnNext = document.getElementById("btnNext");
  const btnPrev = document.getElementById("btnPrev");
  const btnSaveFinal = document.getElementById("btnSaveFinal");
  const stepContainers = document.querySelectorAll(".step-container");

  // === FUNGSI UTILITAS ===
  function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    progressBar.style.width = `${progress}%`;
    progressBar.innerText = `Langkah ${currentStep} / ${totalSteps}`;
  }

  function showStep(step) {
    stepContainers.forEach((div, index) => {
      div.style.display = index === step ? "block" : "none";
    });

    btnPrev.style.display = step <= 1 ? "none" : "inline-block";
    btnNext.style.display = step === totalSteps ? "none" : "inline-block";
    btnSaveFinal.style.display = step === totalSteps ? "inline-block" : "none";

    updateProgress();
  }

  // === STEP 0: Pencarian KK/NIK ===
  const btnCariKK = document.getElementById("btnCariKK");
  const btnLanjutkan = document.getElementById("btnLanjutkan");
  const btnIsiManual = document.getElementById("btnIsiManual");

  if (btnCariKK) {
    btnCariKK.addEventListener("click", async () => {
      const noKK = document.getElementById("no_kk_cari").value.trim();
      const nik = document.getElementById("nik_cari").value.trim();

      if (!noKK && !nik) {
        Swal.fire({
          icon: "warning",
          title: "Masukkan No. KK atau NIK!",
          timer: 2000,
        });
        return;
      }

      try {
        const res = await fetch("/dtsen-usulan/cariKK", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ no_kk: noKK, nik: nik }),
        });
        const data = await res.json();
        const hasilDiv = document.getElementById("hasilPencarian");
        const info = document.getElementById("hasilDataKK");
        hasilDiv.style.display = "block";

        if (data.success && data.data) {
          info.innerHTML = `
            <b>Nama KK:</b> ${data.data.nama}<br>
            <b>No KK:</b> ${data.data.no_kk}<br>
            <b>Alamat:</b> ${data.data.alamat}<br>
            <b>Jumlah ART:</b> ${data.data.jumlah_art}
          `;
          btnLanjutkan.style.display = "inline-block";
          btnIsiManual.style.display = "none";
        } else {
          info.innerHTML = `<p class="text-danger">Data tidak ditemukan.</p>`;
          btnLanjutkan.style.display = "none";
          btnIsiManual.style.display = "inline-block";
        }
      } catch (e) {
        Swal.fire("Gagal", "Tidak dapat menghubungi server", "error");
      }
    });
  }

  // === Memulai usulan baru (setelah cari) ===
  async function startUsulan() {
    try {
      const res = await fetch("/dtsen-usulan/start", { method: "POST" });
      const data = await res.json();

      if (data.success) {
        currentUsulanId = data.usulan_id;
        console.log("🟢 Usulan ID:", currentUsulanId);
        // tampilkan step pertama
        document.getElementById("step-0").style.display = "none";
        showStep(1);
      } else {
        Swal.fire("Gagal", "Tidak dapat memulai usulan baru", "error");
      }
    } catch (e) {
      Swal.fire("Error", "Koneksi gagal saat memulai usulan", "error");
    }
  }

  if (btnLanjutkan)
    btnLanjutkan.addEventListener("click", startUsulan);
  if (btnIsiManual)
    btnIsiManual.addEventListener("click", startUsulan);

  // === Simpan data tiap langkah ===
  async function saveStep() {
    if (!currentUsulanId) {
      console.warn("⚠ Tidak ada ID usulan saat saveStep()");
      return;
    }

    const formData = new FormData(formElement);
    formData.append("usulan_id", currentUsulanId);
    formData.append("step", currentStep);

    try {
      const res = await fetch("/dtsen-usulan/saveStep", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.success) {
        Swal.fire({
          toast: true,
          icon: "success",
          title: "Langkah tersimpan!",
          position: "bottom-end",
          showConfirmButton: false,
          timer: 1000,
        });
      } else {
        Swal.fire("Gagal", "Data tidak tersimpan.", "error");
      }
    } catch (err) {
      Swal.fire("Error", "Gagal menyimpan langkah.", "error");
    }
  }

  // === Navigasi antar step ===
  if (btnNext) {
    btnNext.addEventListener("click", async () => {
      await saveStep();
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
      }
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener("click", () => {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
      }
    });
  }

  // === Final Submit (Step 7) ===
  const btnSubmitFinal = document.getElementById("btnSubmitFinal");
  if (btnSubmitFinal) {
    btnSubmitFinal.addEventListener("click", async () => {
      if (!currentUsulanId) return Swal.fire("Gagal", "ID usulan kosong", "error");

      const catatan = document.getElementById("catatan_verifikasi")?.value || "";
      const signaturePadCanvas = document.getElementById("signature-pad");
      if (!signaturePadCanvas || signaturePadCanvas.getContext("2d").getImageData(0, 0, 1, 1).data[3] === 0) {
        return Swal.fire("Peringatan", "Tanda tangan belum diisi!", "warning");
      }

      const signaturePad = new SignaturePad(signaturePadCanvas);
      const signatureData = signaturePad.toDataURL();

      Swal.fire({
        title: "Yakin simpan usulan ini?",
        text: "Setelah disimpan, data tidak bisa diubah lagi.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Simpan",
        cancelButtonText: "Batal",
      }).then(async (result) => {
        if (result.isConfirmed) {
          const formData = new FormData();
          formData.append("usulan_id", currentUsulanId);
          formData.append("catatan", catatan);
          formData.append("signature", signatureData);

          const res = await fetch("/dtsen-usulan/submitFinal", {
            method: "POST",
            body: formData,
          });
          const data = await res.json();

          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: "Usulan telah dikirim ke Admin.",
            }).then(() => location.reload());
          } else {
            Swal.fire("Gagal", data.message || "Kesalahan server.", "error");
          }
        }
      });
    });
  }

  // === Inisialisasi awal: tampilkan step 0 (pencarian) ===
  showStep(0);
});
